<?php

use yii\helpers\Url;
use yiier\rbac\Module;

$this->title = Yii::t('rbac', 'Rules');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $this->beginContent(Module::getInstance()->mainLayout) ?>


<!-- /section:settings.box -->
<div class="box-group" id="accordion">
    <div class="alert alert-info">
        <p>
            <span href="javascript:;" id="collapsed-setting">
                <i class="fa fa-check"></i>
            </span>
            <?= Yii::t('rbac', 'Please select the rule you want to use.') ?>
        </p>
    </div>
    <!-- PAGE CONTENT BEGINS -->
    <div class="row">
        <div class="col-md-12">

            <ul id="tree-view">
                <?php foreach ($rules as $key => $value): ?>
                    <li>
                        <label>
                            <input name="action" value="<?= $value['class_name'] ?>"
                                   type="checkbox" <?= ($value['check']) ? 'checked' : ''; ?>
                                   class="rule-class_name">
                            <?= $value['class_name'] ?>
                            <input type="text"
                                   class="rule-action-name"
                                   placeholder="<?= Yii::t('rbac', 'Rule Name') ?>"
                                   value="<?= $value['name'] ?>">
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /.page-content-area -->

<div class="urls hidden">
    <a href="<?= Url::toRoute('/rbac/rule/create'); ?>" class="rule-url"></a>
</div>

<?php $this->endContent() ?>
