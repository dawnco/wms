<?php
/**
 * @author Dawnco
 * @date   2022-05-19
 */

namespace Wms\Database;

use PDO;
use PDOStatement;
use Wms\Constant\ErrorCode;
use Wms\Exception\DatabaseException;
use Wms\Fw\Conf;

class WDbConnect
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

    /**
     * @throws DatabaseException
     */
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

    /**
     * @throws DatabaseException
     */
    protected function connect()
    {

        if ($this->dbh instanceof PDO) {
            $this->dbh = null;
        }

        if ($this->retry++ > 3) {
            throw new DatabaseException(sprintf("SQL ERROR connect %s %s fail after retry 3 time",
                $this->config['hostname'],
                $this->config['database'],
            ));
        }

        $dsn =
            "mysql:host={$this->config['hostname']};dbname={$this->config['database']};port={$this->config['port']}charset={$this->config['charset']}";

        try {
            $this->dbh = new PDO($dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']);
        } catch (\PDOException $e) {
            $msg = sprintf("SQL ERROR connect %s %s fail %s", $this->config['hostname'], $this->config['database'],
                $e->getMessage());
            throw new DatabaseException($msg, ErrorCode::DATABASE_ERROR, $e);
        }

        $this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->dbh->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
        $this->dbh->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

        if ($this->config['timezone']) {
            $this->execute('SET time_zone = ?', [$this->config['timezone']]);
        }

        $this->retry = 0; // 重连成功后

    }

    /**
     * 执行一条sql
     * @param string $query
     * @param array  $params
     * @return void
     * @throws DatabaseException
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
     * @throws DatabaseException
     */
    public function insert(string $table, array $data): void
    {
        $this->insertCmd($table, $data, "INSERT");
    }

    /**
     * Replace 一条记录 如果存在PRIMARY或UNIQUE相同的记录，则先删除掉。再插入新记录。
     * @param string $table 表
     * @param array  $data  数据
     * @return void
     * @throws DatabaseException
     */
    public function insertOnReplace(string $table, array $data): void
    {
        $this->insertCmd($table, $data, "REPLACE");
    }

    /**
     * Replace 一条记录 如果存在PRIMARY或UNIQUE相同的记录，则忽略。
     * @param string $table 表
     * @param array  $data  数据
     * @return void
     * @throws DatabaseException
     */
    public function insertOnIgnore(string $table, array $data): void
    {
        $this->insertCmd($table, $data, "INSERT IGNORE");
    }


    /**
     * 如果唯一KEY存在更新表,不存在则插入
     * @param string $table
     * @param array  $data
     */
    public function insertOnDuplicate(string $table, array $data, array $update): void
    {
        $fields = [];
        $values = [];
        $duplicate = [];
        foreach ($data as $field => $value) {
            $fields[] = "`{$field}`";
            $values[] = '?';
        }

        foreach ($update as $field => $value) {
            $duplicate[] = "`$field` = ?";
        }

        $insert_fields = implode(', ', $fields);
        $insert_data = implode(', ', $values);
        $sql =
            "INSERT INTO `{$table}` ({$insert_fields}) values ({$insert_data}) ON DUPLICATE KEY UPDATE " . implode(",",
                $duplicate);

        $val = array_values($data);
        $upVal = array_values($update);
        $this->statement($sql, array_merge($val, $upVal));
    }

    protected function insertCmd(string $table, array $data, string $cmd)
    {
        $fields = array_keys($data);
        $values = array_values($data);
        $fieldsStr = implode("`,`", $fields);
        $holders = implode(',', array_fill(0, count($fields), '?'));
        $query = "$cmd INTO `{$table}` (`{$fieldsStr}`) VALUE ({$holders})";
        $this->statement($query, $values);
    }

    /**
     * 插入一条记录
     * @param string $table
     * @param array  $data
     * @return int 插入的自增ID
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
     */
    public function insertBatch(string $table, array $data): void
    {
        $this->insertBatchCmd($table, $data, 'INSERT');
    }

    /**
     * 批量插入
     * @param string $table
     * @param array  $data
     * @return void
     */
    public function insertBatchOnReplace(string $table, array $data): void
    {
        $this->insertBatchCmd($table, $data, 'REPLACE');
    }

    /**
     * 批量插入 忽略重复主键
     * @param string $table
     * @param array  $data
     * @return void
     */
    public function insertBatchOnIgnore(string $table, array $data): void
    {
        $this->insertBatchCmd($table, $data, 'INSERT IGNORE');
    }

    protected function insertBatchCmd(string $table, array $data, string $cmd)
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

        $query = "$cmd INTO `{$table}` (`{$fieldsStr}`) VALUES {$holders}";
        $this->statement($query, $values);
    }

    /**
     * 删除一条记录
     * @param string $table 表
     * @param array  $where 数据 ['id'=>1, 'pid'=>1]
     * @return void
     * @throws DatabaseException
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
     * @throws DatabaseException
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
     * @throws DatabaseException
     */
    public function getLine(string $query, array $params = [], string $className = 'stdClass')
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
     * @throws DatabaseException
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
     * @throws DatabaseException
     */
    public function getVar(string $query, array $params = [])
    {
        $sth = $this->statement($query, $params);
        $val = $sth->fetchColumn();
        return $val === false ? null : $val;
    }

    public function setRetry(int $time = 3){
        $this->retry = $time;
    }

    /**
     * @param string $query
     * @param array  $params
     * @return PDOStatement
     * @throws DatabaseException
     */
    public function statement(string $query, array $params): PDOStatement
    {
        try {

            if ($this->dbh == null) {
                throw new \PDOException("MySQL server has gone away", ErrorCode::DATABASE_ERROR);
            }

            $sth = $this->dbh->prepare($query);
            $result = $sth->execute($params);

            if (Conf::get('app.env') == 'dev') {
                $this->sql[] = $query . "[" . json_encode($params) . "]";
            }

            if (!$result) {
                $msg = sprintf("SQL ERROR  { %s : %s }", $query, json_encode($params));
                throw new DatabaseException($msg);
            }
            return $sth;
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
                // 重试机制
                $this->connect();
                return $this->statement($query, $params);
            }

            $msg = sprintf("SQL prepare|execute ERROR %s { %s : %s }", $e->getMessage(), $query, json_encode($params));
            throw new DatabaseException($msg, ErrorCode::DATABASE_ERROR, $e);
        }
    }

    /**
     * @throws DatabaseException
     */
    public function begin(): void
    {
        $ret = $this->dbh->beginTransaction();
        if (!$ret) {
            throw new DatabaseException("transaction begin error ");
        }
    }

    /**
     * @throws DatabaseException
     */
    public function commit(): void
    {
        $ret = $this->dbh->commit();
        if (!$ret) {
            throw new DatabaseException("transaction commit error ");
        }
    }

    /**
     * @throws DatabaseException
     */
    public function rollback(): void
    {
        $ret = $this->dbh->rollBack();
        if (!$ret) {
            throw new DatabaseException("transaction rollback error ");
        }
    }

}
