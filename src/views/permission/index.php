<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yiier\rbac\models\AuthRule;

$this->title = Yii::t('rbac', 'Permissions List');
$this->params['breadcrumbs'][] = $this->title;
/** @var array $basePermissions */
?>
<div class="box-group" id="accordion">
    <div class="alert alert-info">
        <p>
            <span href="javascript:;" id="collapsed-setting">
                <i class="fa fa-check"></i>
            </span>
            <?= Yii::t('rbac', 'Please select need to accept access control functions.') ?>
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
                                                <label>
                                                    <input name="action" value="<?= $k ?>"
                                                           type="checkbox" <?= ($v['check']) ? 'checked' : ''; ?>
                                                           class="permission-action permission-name">
                                                    <?= $v['action'] ?>
                                                    <input type="text"
                                                           class="permission-action-des"
                                                           placeholder="<?= Yii::t('rbac', 'Permission Name') ?>"
                                                           value="<?= $v['des'] ?>">
                                                    <?= \yii\helpers\Html::dropDownList(
                                                        'rule_name',
                                                        $v['rule_name'],
                                                        ArrayHelper::map(AuthRule::find()->all(), 'name', 'name'),
                                                        [
                                                            'prompt' => Yii::t('rbac', 'Please select rule'),
                                                            'class' => 'permission-action-rule_name'
                                                        ]
                                                    ) ?>
                                                </label>
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
</div>
<!-- /.page-content-area -->

<div class="urls hidden">
    <a href="<?= Url::toRoute('/rbac/permission/create'); ?>" class="permission-url"></a>
</div>
