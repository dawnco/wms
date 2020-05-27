<el-table
  :border="true"
  :data="entries"
  v-loading="search.loading"
  size="mini"
  stripe
  row-key="id"
  class="custom-table custom-table-sort"
  height="100%"
  ref="table"
  @selection-change="handleSelectionChange"
>
  <el-table-column
    width="40">
    <i class="el-icon-tickets pointer h-sort" title="拖动移动"></i>
  </el-table-column>
  <el-table-column
    type="selection"
    width="50">
  </el-table-column>
  <el-table-column label="ID" width="100" prop="id">
  </el-table-column>
    <?php foreach ($fields as $v): ?>
      <el-table-column label="<?= $v['Comment'] ?>" prop="<?= $v['Field'] ?>">
      </el-table-column>
    <?php endforeach; ?>
</el-table>
