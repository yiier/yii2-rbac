<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = Yii::t('rbac', 'Users');
$this->params['breadcrumbs'][] = $this->title;
$auth = Yii::$app->authManager;

/**  @var \yiier\rbac\models\AuthItem $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $usernameField */
/** @var string $idField */

?>
<div class="box">

    <div class="box-body">

        <div class="search-form">

            <?php $form = ActiveForm::begin([
                'layout' => 'inline',
                'method' => 'get',
                'action' => ['index']
            ]); ?>
            <?= $form->field($searchModel, $idField)->textInput(['placeholder' => Yii::t('rbac', 'ID')]) ?>

            <?= $form->field($searchModel, $usernameField)->textInput(['placeholder' => Yii::t('rbac', 'Username')]) ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('rbac', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('rbac', 'Reset'), ['index'], ['class' => 'btn btn-default']) ?>
            </div>

        </div>
        <?php ActiveForm::end(); ?>

        <?php
        Pjax::begin([
            'enablePushState' => false,
        ]);
        ?>

        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{items}\n{summary}\n{pager}",
            'options' => [
                'class' => 'grid-view table-responsive rbac-grid'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                $idField,
                $usernameField,
                [
                    'header' => Yii::t('rbac', 'Roles'),
                    'format' => 'raw',
                    'value' => function ($model) use ($auth, $idField) {
                        $roles = $auth->getRolesByUser($model->{$idField});
                        return implode(' | ', array_keys($roles));
                    }
                ],
                [
                    'header' => Yii::t('rbac', 'Actions'),
                    'format' => 'raw',
                    'value' => function ($model) use ($idField) {
                        $url = Url::to(['/rbac/user/view', 'id' => $model->{$idField}]);
                        $options = [
                            'title' => Yii::t('rbac', 'View Permission'),
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip'
                        ];
                        return Html::a('<span class="fa fa-fire btn btn-xs btn-primary"></span>', $url, $options);
                    }
                ],
            ],
        ]);
        Pjax::end();
        ?>
    </div>
</div>

