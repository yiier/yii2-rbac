<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019/4/23 8:06 PM
 * description:
 */

namespace yiier\rbac\widgets;


use yiier\rbac\helpers\AuthHelper;

class Menu extends \yii\widgets\Menu
{
    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->ensureVisibility($this->items);
    }

    /**
     * @param array $items
     *
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    protected function ensureVisibility(&$items)
    {
        $allVisible = false;
        foreach ($items as &$item) {
            if (isset($item['url']) AND !in_array($item['url'], ['', '#']) AND !isset($item['visible'])) {
                $item['visible'] = AuthHelper::canRoute($item['url']);
            }
            if (isset($item['items'])) {
                // If not children are visible - make invisible this node
                if (!$this->ensureVisibility($item['items']) AND !isset($item['visible'])) {
                    $item['visible'] = false;
                }
            }
            if (isset($item['label']) AND (!isset($item['visible']) OR $item['visible'] === true)) {
                $allVisible = true;
            }
        }
        return $allVisible;
    }
}