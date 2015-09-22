<?php

use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\SearchUser $searchModel
 */

$this->title = Yii::t('rbac', 'Assigned User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Roles'), 'url' => ['role/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-content">
    <div class="page-content-area">
        <p><?= Yii::t('rbac', 'Click{name}, background color change after says it has chosen', ['name' => Yii::t('rbac', 'User')]) ?></p>

        <div class="row">
            <div class="col-xs-12">
                <?php foreach ($user as $k => $v): ?>
                    <h5>
                        <a href="#" class="btn btn-xs pinyin" title="<?= Yii::t('rbac', 'Check All') ?>">
                            <?= $k ?>(<?= Yii::t('rbac', 'Click on the selection') ?>)
                        </a>
                    </h5>
                    <ul class="u-list">
                        <?php foreach ($v as $key => $val): ?>
                            <li rel="user-id" data-user_id="<?= $key ?>"
                                class="user<?php if ($val['is_sel'] == 1) echo ' selected'; ?>">
                                <?= $val['username'] ?>
                            </li>
                        <?php endforeach; ?>
                        <div class="clearfix"></div>
                    </ul>
                <?php endforeach; ?>
                <input type="hidden" name='csrf' value="<?= Yii::$app->request->getCsrfToken() ?>">
                <input type="hidden" name="role_name" value="<?= Yii::$app->request->get('name'); ?>">
            </div>
        </div>
    </div>
</div>

<div class="urls hidden">
    <a href="<?= Url::toRoute('/rbac/role/assign'); ?>" class="role-assign"></a>
</div>