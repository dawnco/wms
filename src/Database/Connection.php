<?php
/**
 * @author Dawnco
 * @date   2022-05-19
 */

namespace Wms\Database;

use PDO;
use PDOStatement;
use Wms\Constant\ErrorCode;
use Wms\Exception\PageNotFoundException;
use Wms\Fw\Conf;

class Connection
{


    protected ?PDO $dbh = null;

    protected array $config = [];

    /**
     * @var int 连接了多少次
     */
    protected int $retry = 0;

    /**
     * 执行过的sql
     * @var array
     */
    public array $sql = [];

    public function __construct(array $config)
    {
        $this->config['hostname'] = $config['hostname'] ?? '127.0.0.1';
        $this->config['database'] = $config['database'] ?? '';
        $this->config['port'] = $config['port'] ?? 3306;
        $this->config['username'] = $config['username'] ?? null;
        $this->config['password'] = $config['password'] ?? null;
        $this->config['charset'] = $config['charset'] ?? 'utf8mb4';
        $this->config['options'] = $config['options'] ?? null;
        $this->config['timezone'] = $config['timezone'] ?? null;

        $this->connect();
    }

    protected function connect()
    {

        if ($this->dbh instanceof PDO) {
            $this->dbh = null;
        }

        if ($this->retry++ > 3) {
            throw new PageNotFoundException(sprintf("SQL ERROR connect %s fail after retry 3 time",
                $this->config['hostname']));
        }

        $dsn =
            "mysql:host={$this->config['hostname']};dbname={$this->config['database']};port={$this->config['port']}charset={$this->config['charset']}";

        try {
            $this->dbh = new PDO($dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']);
        } catch (\PDOException $e) {
            $msg = sprintf("SQL ERROR connect %s fail %s", $this->config['hostname'], $e->getMessage());
            throw new PageNotFoundException($msg, ErrorCode::DATABASE_ERROR, $e);
        }

        $this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->dbh->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
        $this->dbh->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

        if ($this->config['timezone']) {
            $this->execute('SET time_zone = ?', [$this->config['timezone']]);
        }

    }

    /**
     * 执行一条sql
     * @param string $query
     * @param array  $params
     * @return void
     * @throws PageNotFoundException
     */
    public function execute(string $query, array $params = []): void
    {
        $this->statement($query, $params);
    }

    /**
     *  插入一条记录
     * @param string $table 表
     * @param array  $data  数据
     * @return void
     * @throws PageNotFoundException
     */
    public function insert(string $table, array $data): void
    {
        $fields = array_keys($data);
        $values = array_values($data);
        $fieldsStr = implode("`,`", $fields);
        $holders = implode(',', array_fill(0, count($fields), '?'));
        $query = "INSERT INTO `{$table}` (`{$fieldsStr}`) VALUE ({$holders})";

        $this->statement($query, $values);

    }

    /**
     * Replace 一条记录 如果存在PRIMARY或UNIQUE相同的记录，则先删除掉。再插入新记录。
     * @param string $table 表
     * @param array  $data  数据
     * @return void
     * @throws PageNotFoundException
     */
    public function replace(string $table, array $data): void
    {
        $fields = array_keys($data);
        $values = array_values($data);
        $fieldsStr = implode("`,`", $fields);
        $holders = implode(',', array_fill(0, count($fields), '?'));
        $query = "REPLACE INTO `{$table}` (`{$fieldsStr}`) VALUE ({$holders})";
        $this->statement($query, $values);
    }

    /**
     * 插入一条记录
     * @param string $table
     * @param array  $data
     * @return int 插入的自增ID
     * @throws PageNotFoundException
     */
    public function insertGetId(string $table, array $data): int
    {
        $this->insert($table, $data);
        return (int)$this->dbh->lastInsertId();
    }

    /**
     * 批量插入
     * @param string $table
     * @param array  $data
     * @return void
     * @throws PageNotFoundException
     */
    public function insertBatch(string $table, array $data = []): void
    {
        $fields = array_keys($data[0]);
        $fieldsStr = implode("`,`", $fields);
        $holdersOne = implode(',', array_fill(0, count($fields), '?'));

        $holders = "";
        $values = [];
        foreach ($data as $v) {
            $holders .= "(" . $holdersOne . "),";
            foreach ($v as $lv) {
                $values [] = $lv;
            }
        }

        $holders = rtrim($holders, ",");

        $query = "INSERT INTO `{$table}` (`{$fieldsStr}`) VALUES {$holders}";
        $this->statement($query, $values);
    }

    /**
     * 删除一条记录
     * @param string $table 表
     * @param array  $where 数据 ['id'=>1, 'pid'=>1]
     * @return void
     * @throws PageNotFoundException
     */
    public function delete(string $table, array $where): void
    {
        $values = [];
        $s = [];
        foreach ($where as $k => $v) {
            $s[] = " `$k` = ? ";
            $values[] = $v;
        }
        $values = array_values($where);
        $query = "DELETE FROM `{$table}` WHERE " . implode("AND", $s);
        $this->statement($query, $values);
    }


    /**
     * 更新记录
     * @param string $table 表
     * @param array  $data  数据
     * @param array  $where 条件 例如 ['id'=>1]
     * @return void
     * @throws PageNotFoundException
     */
    public function update(string $table, array $data, array $where): void
    {

        $params = [];
        $updateSets = [];
        $whereSets = [];
        foreach ($data as $field => $value) {
            $updateSets[] = sprintf('`%s` = ?', $field);
            $params[] = $value;
        }

        foreach ($where as $field => $value) {
            $whereSets[] = sprintf('`%s` = ?', $field);
            $params[] = $value;
        }

        $updateSetsStr = implode(",", $updateSets);
        $whereSetsStr = implode(" AND ", $whereSets);

        $query = "UPDATE `{$table}` SET $updateSetsStr WHERE 1 AND $whereSetsStr";

        $this->statement($query, $params);

    }

    /**
     * 获取一条记录
     * @param string $query     sql语句
     * @param array  $params    绑定值
     * @param string $className 结果对象
     * @return mixed
     * @throws PageNotFoundException
     */
    public function getLine(string $query, array $params = [], string $className = 'stdClass'): mixed
    {
        $sth = $this->statement($query, $params);
        $sth->setFetchMode(PDO::FETCH_CLASS, $className);
        return $sth->fetch(PDO::FETCH_CLASS) ?: null;
    }

    /**
     * 获取结果
     * @param string $query     sql语句
     * @param array  $params    绑定值
     * @param string $className 结果映射类
     * @return array
     * @throws PageNotFoundException
     */
    public function getData(string $query, array $params = [], string $className = 'stdClass'): array
    {
        $sth = $this->statement($query, $params);
        return $sth->fetchAll(PDO::FETCH_CLASS, $className) ?: [];
    }

    /**
     * 获取一个值
     * @param string $query  sql语句
     * @param array  $params 绑定值
     * @return mixed
     * @throws PageNotFoundException
     */
    public function getVar(string $query, array $params = [])
    {
        $sth = $this->statement($query, $params);
        return $sth->fetchColumn() ?: null;
    }

    /**
     * @param string $query
     * @param array  $params
     * @return PDOStatement
     * @throws PageNotFoundException
     */
    public function statement(string $query, array $params): PDOStatement
    {
        try {
            $sth = $this->dbh->prepare($query);
            $result = $sth->execute($params);

            if (Conf::get('app.env') == 'dev') {
                $this->sql[] = $query . "[" . json_encode($params) . "]";
            }

            if (!$result) {
                $msg = sprintf("SQL ERROR  { %s : %s }", $query, json_encode($params));
                throw new PageNotFoundException($msg);
            }
            return $sth;
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'MySQL server has gone away')) {
                // 重试机制
                $this->connect();
                return $this->statement($query, $params);
            }

            $msg = sprintf("SQL ERROR  %s { %s : %s }", $e->getMessage(), $query, json_encode($params));
            throw new PageNotFoundException($msg, ErrorCode::DATABASE_ERROR, $e);
        }
    }

    /**
     * @throws PageNotFoundException
     */
    public function begin(): void
    {
        $ret = $this->dbh->beginTransaction();
        if (!$ret) {
            throw new PageNotFoundException("transaction begin error ");
        }
    }

    /**
     * @throws PageNotFoundException
     */
    public function commit(): void
    {
        $ret = $this->dbh->commit();
        if (!$ret) {
            throw new PageNotFoundException("transaction commit error ");
        }
    }

    /**
     * @throws PageNotFoundException
     */
    public function rollback(): void
    {
        $ret = $this->dbh->rollBack();
        if (!$ret) {
            throw new PageNotFoundException("transaction rollback error ");
        }
    }

}
