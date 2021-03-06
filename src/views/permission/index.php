<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yiier\rbac\helpers\Html;
use yiier\rbac\models\AuthRule;
use yiier\rbac\Module;

$this->title = Yii::t('rbac', 'Permissions List');
$this->params['breadcrumbs'][] = $this->title;
/** @var array $basePermissions */
?>
<?php $this->beginContent(Module::getInstance()->mainLayout) ?>

<div class="alert alert-info">
    <p>
            <span href="javascript:;" id="collapsed-setting">
                <i class="fa fa-check"></i>
            </span>
        <?= Yii::t('rbac', 'Please select need to accept access control functions.') ?>
        <span class="pull-right">
            <?= Html::a(Yii::t('rbac', 'Clear Cache'), ['clear-cache']) ?>
        </span>
    </p>
</div>
<!-- PAGE CONTENT BEGINS -->
<div class="row">
    <div class="col-md-12">

        <ul id="tree-view">
            <?php foreach ($basePermissions as $key => $basePermission): ?>
                <li>
                    <i class="fa fa-minus collapsed"></i>
                    <label>
                        <?php echo $key; ?>
                    </label>
                    <ul>
                        <?php foreach ($basePermission as $ke => $val): ?>
                            <li><i class="fa fa-minus collapsed"></i>
                                <label>
                                    <?php echo $ke; ?>
                                </label>
                                <ul>
                                    <?php foreach ($val as $k => $v): ?>
                                        <li>
                                            <label class="permission-action-label">
                                                <input name="action" value="<?= $k ?>"
                                                       type="checkbox" <?= ($v['check']) ? 'checked' : ''; ?>
                                                       class="permission-action permission-name">
                                                <?= $v['action'] ?>
                                            </label>
                                            <label class="permission-action-des-label">
                                                <input type="text"
                                                       class="permission-action-des"
                                                       placeholder="<?= Yii::t('rbac', 'Permission Name') ?>"
                                                       value="<?= $v['des'] ?>">
                                            </label>
                                            <?= \yii\helpers\Html::dropDownList(
                                                'rule_name',
                                                $v['rule_name'],
                                                ArrayHelper::map(AuthRule::find()->all(), 'name', 'name'),
                                                [
                                                    'prompt' => Yii::t('rbac', 'Please select rule'),
                                                    'class' => 'permission-action-rule_name' . (Module::getInstance()->rulesPath ? '' : ' hidden')
                                                ]
                                            ) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<!-- /.row -->
<?php $this->endContent() ?>

<!-- /.page-content-area -->

<div class="urls hidden">
    <a href="<?= Url::toRoute('/rbac/permission/create'); ?>" class="permission-url"></a>
</div>
