<?php

$this->title = Yii::t('rbac', 'View Permission');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Users'), 'url' => ['/rbac/user']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if ($permissions): ?>
    <div class="col-xs-12">
        <?php foreach ($permissions as $k => $v): ?>
            <h5><?= $k ?></h5>
            <ul class="u-list">
                <?php foreach ($v as $key => $val): ?>
                    <li rel="user-id" data-id="<?= $val['name'] ?>" class="assign-permissions selected">
                        <?= ($val['description']) ? $val['description'] : $val['name'] ?>
                    </li>
                <?php endforeach; ?>
                <div class="clearfix"></div>
            </ul>
        <?php endforeach; ?>
    </div>

<?php else: ?>
    <?= Yii::t('rbac', 'No Data') ?>
<?php endif ?>

