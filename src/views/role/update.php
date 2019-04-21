<?php

/* @var $this yii\web\View */

use yiier\rbac\Module;

/* @var $model yiier\rbac\models\AuthItem */

$this->title = Yii::t('rbac', 'Update') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $this->beginContent(Module::getInstance()->mainLayout) ?>

<?= $this->render('_form', [
    'model' => $model,
]); ?>

<?php $this->endContent() ?>
