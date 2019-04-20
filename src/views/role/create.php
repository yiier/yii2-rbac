<?php

/* @var $this yii\web\View */
/* @var $model yiier\rbac\models\AuthItem */

$this->title = Yii::t('rbac', 'Create Role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('_form', [
    'model' => $model,
]);

