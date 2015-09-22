<?php
/**
 * author     : forecho <caizh@snsshop.com>
 * createTime : 2015/5/21 16:50
 * description:
 */

namespace yiier\rbac\assets;

use yii\web\AssetBundle;

class RbacAsset extends AssetBundle
{
    public $sourcePath = '@yiier/rbac/web';
//    public $baseUrl = '@web';
    public $css = [
        'css/common.css',
    ];
    public $js = [
        'js/rbac.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
