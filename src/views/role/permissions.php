<?php

use yii\helpers\Url;
use yiier\rbac\Module;

$this->title = Yii::t('rbac', 'Assigned Permission');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Roles'), 'url' => ['role/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $this->beginContent(Module::getInstance()->mainLayout) ?>

<div class="row">
    <div class="col-xs-12">
        <p><?= Yii::t('rbac', 'Click{name}, background color change after says it has chosen',
                ['name' => Yii::t('rbac', 'Permission')]) ?></p>

        <?php foreach ($permissions as $k => $v): ?>
            <h5>
                <a href="#" class="btn btn-xs assign-all-permissions" title="<?= Yii::t('rbac', 'Check All') ?>">
                    <?= $k ?>(<?= Yii::t('rbac', 'Click on the selection') ?>)
                </a>
            </h5>
            <ul class="u-list">
                <?php foreach ($v as $key => $val): ?>
                    <li rel="user-id" data-id="<?= $val['name'] ?>"
                        class="assign-permissions<?php if ($val['is_sel']) {
                            echo ' selected';
                        } ?>">
                        <?= ($val['description']) ? $val['description'] : $val['name'] ?>
                    </li>
                <?php endforeach; ?>
                <div class="clearfix"></div>
            </ul>
        <?php endforeach; ?>
        <input type="hidden" name='csrf' value="<?= Yii::$app->request->getCsrfToken() ?>">
        <input type="hidden" name="role_name" value="<?= Yii::$app->request->get('name'); ?>">
    </div>
</div>

<div class="urls hidden">
    <a href="<?= Url::toRoute('/rbac/role/assign-permissions'); ?>" class="role-assign"></a>
</div>

<?php $this->endContent() ?>
