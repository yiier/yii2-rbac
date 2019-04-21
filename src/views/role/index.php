<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yiier\rbac\Module;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \yiier\rbac\models\AuthItem $searchModel
 */

$this->title = Yii::t('rbac', 'Roles');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $this->beginContent(Module::getInstance()->mainLayout) ?>

<p>
    <?= Html::a(Yii::t('rbac', 'Create Role'), ['create'], ['class' => 'btn btn-success']) ?>
</p>

<div class="search-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'inline',
        'method' => 'get',
        'action' => ['index']
    ]); ?>
    <?= $form->field($searchModel, 'name')->textInput(['placeholder' => Yii::t('rbac', 'Role Name')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rbac', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('rbac', 'Reset'), ['index'], ['class' => 'btn btn-default']) ?>
    </div>

</div>
<?php ActiveForm::end(); ?>


<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "{items}\n{summary}\n{pager}",
    'options' => [
        'class' => 'grid-view table-responsive rbac-grid'
    ],
    'columns' => [
        'name',
        'description',
        'created_at:datetime',
        'updated_at:datetime',
        [
            'header' => Yii::t('rbac', 'Actions'),
            'class' => 'yiier\rbac\widgets\ActionColumn',
            'template' => '{update} {assign-permissions}  {assign-user} {delete}',
            'buttons' => [
                // 自定义按钮
                'assign-user' => function ($url, $model, $key) {
                    $options = [
                        'title' => Yii::t('rbac', 'Assigned User'),
                        'data-pjax' => '0',
                        'data-toggle' => 'tooltip'
                    ];
                    return Html::a('<span class="fa fa-user btn btn-xs btn-primary"></span>', $url, $options);
                },
                'assign-permissions' => function ($url, $model, $key) {
                    $options = [
                        'title' => Yii::t('rbac', 'Assigned Permission'),
                        'data-pjax' => '0',
                        'data-toggle' => 'tooltip'
                    ];
                    return Html::a('<span class="fa fa-fire btn btn-xs btn-primary"></span>', $url, $options);
                },
            ],
            'urlCreator' => function ($action, $model, $key, $index) {
                switch ($action) {
                    case 'update':
                        $link = Yii::$app->getUrlManager()->createUrl([
                            '/rbac/role/update',
                            'name' => $model->name
                        ]);
                        break;
                    case 'delete':
                        $link = Yii::$app->getUrlManager()->createUrl([
                            '/rbac/role/delete',
                            'name' => $model->name
                        ]);
                        break;
                    case 'assign-user':
                        $link = Yii::$app->getUrlManager()->createUrl([
                            '/rbac/role/user',
                            'name' => $model->name
                        ]);
                        break;
                    case 'assign-permissions':
                        $link = Yii::$app->getUrlManager()->createUrl([
                            '/rbac/role/permissions',
                            'name' => $model->name
                        ]);
                        break;
                    default:
                        $link = '#';
                        break;
                }
                return $link;
            },
        ],
    ],
]); ?>


<?php $this->endContent() ?>
