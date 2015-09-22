<?php
yiier\rbac\assets\RbacAsset::register($this);

isset($this->params['breadcrumbs']) ?: $this->params['breadcrumbs'] = [];
array_unshift($this->params['breadcrumbs'], ['label' => Yii::t('rbac', 'RBAC'), 'url' => ['index']]);
?>
<?php $this->beginContent('@backend/views/layouts/main.php') ?>
<?= $content ?>
<?php $this->endContent() ?>