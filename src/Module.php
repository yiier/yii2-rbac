<?php

namespace yiier\rbac;

use Yii;

class Module extends \yii\base\Module
{
    public $layout = 'column';

    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->i18n->translations['rbac-admin'])) {
            Yii::$app->i18n->translations['rbac'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@yiier/rbac/messages'
            ];
        }
        \Yii::configure($this, require(__DIR__ . '/config.php'));
    }
}