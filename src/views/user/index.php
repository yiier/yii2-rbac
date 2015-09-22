<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = Yii::t('rbac', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
Pjax::begin([
    'enablePushState' => false,
]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'class' => 'yii\grid\DataColumn',
            'attribute' => $usernameField,
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}'
        ],
    ],
]);
Pjax::end();
?>

