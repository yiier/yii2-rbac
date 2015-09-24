<?php

namespace yiier\rbac;

use Yii;

class Module extends \yii\base\Module
{
    public $layout = 'column';
    public $allowNamespaces = [];
    public $ignoreModules = ['gii', 'debug'];
    public $menus = [];
    private $_coreItems = [
        'rbac' => '权限系统',
        'permissions' => '权限列表',
        'roles' => '角色列表',
        'users' => '用户列表',
    ];

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
    }

    public function getItems()
    {
        return [
            'label' => isset($this->menus['rbac']) ? $this->menus['rbac'] : $this->_coreItems['rbac'],
            'url' => ['#'],
            'icon' => 'fa fa-cog',
            'options' => [
                'class' => 'treeview',
            ],
            'items' => [
                [
                    'label' => isset($this->menus['permissions']) ? $this->menus['permissions'] : $this->_coreItems['permissions'],
                    'url' => ['/rbac'],
                    'icon' => 'fa fa-user',
                ],
                [
                    'label' => isset($this->menus['roles']) ? $this->menus['roles'] : $this->_coreItems['roles'],
                    'url' => ['/rbac/role'],
                    'icon' => 'fa fa-user',
                ],
                [
                    'label' => isset($this->menus['users']) ? $this->menus['users'] : $this->_coreItems['users'],
                    'url' => ['/rbac/user'],
                    'icon' => 'fa fa-user',
                ]
            ],
        ];
    }
}