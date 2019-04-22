<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019/4/22 4:11 PM
 * description:
 */

namespace yiier\rbac\helpers;


class Html extends \yii\helpers\Html
{
    /**
     * @param string $text
     * @param null $url
     * @param array $options
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public static function a($text, $url = null, $options = [])
    {
        if (in_array($url, [null, '', '#'])) {
            return parent::a($text, $url, $options);
        }
        return AuthHelper::canRoute($url) ? parent::a($text, $url, $options) : '';
    }

    /**
     *
     * @inheritdoc
     */
    public static function checkbox($name, $checked = false, $options = [])
    {
        $options['checked'] = (bool)$checked;
        $value = array_key_exists('value', $options) ? $options['value'] : '1';
        if (isset($options['uncheck'])) {
            // add a hidden field so that if the checkbox is not selected, it still submits a value
            $hidden = static::hiddenInput($name, $options['uncheck']);
            unset($options['uncheck']);
        } else {
            $hidden = '';
        }
        $label = (isset($options['label'])) ? $options['label'] : ' ';
        $labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
        unset($options['label'], $options['labelOptions']);
        $content = static::input('checkbox', $name, $value, $options) . static::label($label, null, $labelOptions);
        return '<div class="checkbox">' . $hidden . $content . '</div>';
    }
}