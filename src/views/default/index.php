<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('rbac', 'Permissions List');
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- /section:settings.box -->
<div class="box-group" id="accordion">
    <div class="alert alert-info">
        <p><?= Yii::t('rbac', 'Please select need to accept access control functions.')?><a href="javascript:;" class="pull-right" id="collapsed-setting"><i class="fa fa-plus"></i></a></p>

    </div>
    <!-- PAGE CONTENT BEGINS -->
    <div class="row">
        <div class="col-md-12">

            <?php foreach ($classes as $key => $value): ?>
                <div class="box box-solid box-default collapsed collapsed-box">
                    <div class="box-header with-border">
                        <h4 class="box-title"><?php echo $key; ?></h4>

                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <div class="box-body">

                        <?php foreach ($value as $ke => $val): ?>
                            <div class="box box-default collapsed collapsed-box">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?php echo $ke; ?></h3>

                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <?php foreach ($val as $k => $v): ?>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <input name="action" value="<?= $k ?>" type="checkbox" <?= ($v['check']) ? 'checked' : ''; ?> class="action permission-name">
                                            </span>
                                            <span class="input-group-addon bg-gray color-palette"><?= $v['action'] ?></span>
                                            <input type="text" class="form-control action-des" value="<?= $v['des'] ?>">
                                        </div>
                                        <br>
                                    <?php endforeach; ?>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /.page-content-area -->

<div class="urls hidden">
    <a href="<?= Url::toRoute('/rbac/default/create-permission'); ?>" class="permission"></a>
</div>
