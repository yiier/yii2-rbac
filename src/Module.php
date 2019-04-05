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
     * @var string Main layout using for module. Default to layout of parent module.
     * Its used when `layout` set to 'left-menu', 'right-menu' or 'top-menu'.
     */
    public $mainLayout = '@yiier/rbac/views/layouts/main.php';

    private $defaultMenus = [
        'rbac' => '权限系统',
        'permissions' => '权限列表',
        'roles' => '角色列表',
        'users' => '用户列表',
        'rules' => '规则列表',
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
        RbacAsset::register(Yii::$app->view);
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