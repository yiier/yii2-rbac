<?php

use yii\helpers\Url;

$this->title = Yii::t('rbac', 'Permissions List');
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- /section:settings.box -->
<div class="box-group" id="accordion">
    <div class="alert alert-info">
        <p>
            <?= Yii::t('rbac', 'Please select need to accept access control functions.') ?>
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