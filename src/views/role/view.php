<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model yiier\rbac\models\AuthItem */

$this->title = Yii::t('yii', 'View') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title

?>
<div class="auth-item-view">

    <p>
        <?= Html::a(Yii::t('rbac', 'Update'), ['update', 'name' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('rbac', 'Delete'), ['delete', 'name' => $model->name], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            //'type',
            'description:text',
            'ruleName',
            'data',
            //'created_at',
            //'updated_at',
        ],
    ]) ?>

</div>
