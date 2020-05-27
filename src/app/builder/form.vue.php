<custom-dialog v-model="visible" :title="visibleTitle" @ok="handleSave" :loading="loading" :data-loading="dataLoading">
  <el-form :model="formData" :label-width="webConfig.labelWidth">
    <el-form-item label="ID" v-if="formData.id">
      {{formData.id}}
    </el-form-item>
      <?php foreach ($fields as $v): ?>
        <el-form-item label="<?= $v['Comment'] ?>" required>
          <el-input v-model="formData.<?= $v['Field'] ?>" clearable></el-input>
        </el-form-item>
      <?php endforeach; ?>
  </el-form>
</custom-dialog>
