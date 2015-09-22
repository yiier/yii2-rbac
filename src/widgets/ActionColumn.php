<?php
/**
 * author     : forecho <caizh@snsshop.com>
 * createTime : 2015/5/28 11:16
 * description:
 */

namespace yiier\rbac\widgets;

use Yii;
use yii\helpers\Html;

class ActionColumn extends \yii\grid\ActionColumn
{


    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model) {
                return Html::a('<span class="fa fa-eye btn btn-xs btn-primary"></span>', $url, [
                    'title' => Yii::t('yii', 'View'),
                    'data-pjax' => '0',
                    'data-toggle' => 'tooltip'
                ]);
            };
        }
        if (!isset($this->buttons['setting'])) {
            $this->buttons['setting'] = function ($url, $model) {
                return Html::a('<span class="fa fa-cog btn btn-xs btn-primary"></span>', $url, [
                    'title' => Yii::t('app', 'Setting'),
                    'data-pjax' => '0',
                    'data-toggle' => 'tooltip'
                ]);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model) {
                return Html::a('<span class="fa fa-edit btn btn-xs btn-primary"></span>', $url, [
                    'title' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                    'data-toggle' => 'tooltip'
                ]);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model) {
                return Html::a('<span class="fa fa-trash btn btn-xs btn-danger"></span>', $url, [
                    'title' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'data-toggle' => 'tooltip'
                ]);
            };
        }
    }

    /**
     * 重写了标签渲染方法。
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return mixed
     */
    protected function renderDataCellContent($model, $key, $index)
    {

        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];
            if (isset($this->buttons[$name])) {
                $type = $name;
            } else {
                $type = 'setting';
            }
            $url = $this->createUrl($name, $model, $key, $index);

            return call_user_func($this->buttons[$type], $url, $model, $key);
        }, $this->template);

    }
}