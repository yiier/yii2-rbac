<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019/4/22 10:29 PM
 * description:
 */

namespace yiier\rbac\helpers;


use Yii;
use yii\helpers\ArrayHelper;

class Config
{
    const CACHE_TAG = 'yiier.rbac';
    const CONFIG_KEY = 'yiier.rbac.config';

    public $menus = [];
    public $superManId;
    public $cacheDuration = 3600 * 30; // default 30 day

    /**
     * @var self Instance of self
     */
    private static $_instance;


    /**
     * Create instance of self
     * @return Config|object
     * @throws \yii\base\InvalidConfigException
     */
    public static function instance()
    {
        if (self::$_instance === null) {
            $type = ArrayHelper::getValue(Yii::$app->params, self::CONFIG_KEY, []);
            if (is_array($type) && !isset($type['class'])) {
                $type['class'] = static::class;
            }
            return self::$_instance = Yii::createObject($type);
        }
        return self::$_instance;
    }

    /**
     * @param $name
     * @param $arguments
     * @return null
     * @throws \yii\base\InvalidConfigException
     */
    public static function __callStatic($name, $arguments)
    {
        $instance = static::instance();
        if ($instance->hasProperty($name)) {
            return $instance->$name;
        } else {
            if (count($arguments)) {
                $instance->options[$name] = reset($arguments);
            } else {
                return array_key_exists($name, $instance->options) ? $instance->options[$name] : null;
            }
        }
    }
}