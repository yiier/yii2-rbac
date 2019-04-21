<?php

namespace yiier\rbac;

use Yii;
use yiier\rbac\assets\RbacAsset;

class Module extends \yii\base\Module
{
    public $allowNamespaces = [];
    public $ignoreModules = ['gii', 'debug'];
    public $menus = [];
    public $rulesPath = [];

    /**
     * @var string
     */
    public $mainLayout = '@yiier/rbac/views/layout.php';

    /**
     * @var array
     */
    private $defaultMenus = [];

    // users
    public $userClassName;
    public $idField = 'id';
    public $usernameField = 'username';
    public $searchClass;

    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->i18n->translations['rbac'])) {
            Yii::$app->i18n->translations['rbac'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@yiier/rbac/messages'
            ];
        }
        RbacAsset::register(Yii::$app->view);
        $this->defaultMenus = [
            'rbac' => Yii::t('rbac', 'RBAC'),
            'permissions' => Yii::t('rbac', 'Permissions'),
            'roles' => Yii::t('rbac', 'Roles'),
            'users' => Yii::t('rbac', 'Users'),
            'rules' => Yii::t('rbac', 'Rules'),
        ];
        $this->menus = array_merge($this->menus, $this->defaultMenus);
    }

    public function getItems()
    {
        return [
            'label' => $this->menus['rbac'],
            'url' => ['#'],
            'icon' => 'fa fa-cog',
            'options' => [
                'class' => 'treeview',
            ],
            'items' => [
                [
                    'label' => $this->menus['permissions'],
                    'url' => ['/rbac/permission/index'],
                    'icon' => 'fa fa-user',
                ],
                [
                    'label' => $this->menus['roles'],
                    'url' => ['/rbac/role/index'],
                    'icon' => 'fa fa-user',
                ],
                [
                    'label' => $this->menus['users'],
                    'url' => ['/rbac/user/index'],
                    'icon' => 'fa fa-user',
                ],
                [
                    'label' => $this->menus['rules'],
                    'url' => ['/rbac/rule/index'],
                    'icon' => 'fa fa-user',
                ],
            ],
        ];
    }
}