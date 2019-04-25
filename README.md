RBAC Manager for Yii 2
==============
RBAC Auth manager for Yii2

[![Latest Stable Version](https://poser.pugx.org/yiier/yii2-rbac/v/stable)](https://packagist.org/packages/yiier/yii2-rbac) 
[![Total Downloads](https://poser.pugx.org/yiier/yii2-rbac/downloads)](https://packagist.org/packages/yiier/yii2-rbac) 
[![Latest Unstable Version](https://poser.pugx.org/yiier/yii2-rbac/v/unstable)](https://packagist.org/packages/yiier/yii2-rbac) 
[![License](https://poser.pugx.org/yiier/yii2-rbac/license)](https://packagist.org/packages/yiier/yii2-rbac)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiier/yii2-rbac "*"
```

or add

```
"yiier/yii2-rbac": "*"
```

to the require section of your `composer.json` file.


Usage
-----

### 安装

- 修改配置文件，比如 修改 `config/main.php`， 添加：

```php
return [
    'modules' => [
        'rbac' => [
            'class' => 'yiier\rbac\Module',
            // 'ignoreModules' => ['gii', 'debug', 'rbac'],
            // 'menus' => [
            //    'rbac' =>'RBAC',
            //    'permissions' =>'Permissions',
            //    'roles' =>'Roles',
            //    'users' =>'Users',
            //    'rules' =>'Rules',
            // ],
            // 'userClassName' => 'app\models\User',
            // 'idField' => 'id',
            // 'usernameField' => 'shop_name',
            // 'mainLayout' => '@app/views/layout/rbac.php',
            // 'isAdvanced' => true, // 如果使用的是高级模板
            // 'advancedConfigs' = [ //如果你还有其他应用需要加入控制
            //     [
            //           '@common/config/main.php',
            //           '@common/config/main-local.php',
            //           '@frontend/config/main.php',
            //           '@frontend/config/main-local.php',
            //      ],
            //      [
            //           '@common/config/main.php',
            //           '@common/config/main-local.php',
            //           '@api/config/main.php',
            //           '@api/config/main-local.php',
            //      ],
            //      [
            //           '@common/config/main.php',
            //           '@common/config/main-local.php',
            //           '@backend/config/main.php',
            //           '@backend/config/main-local.php',
            //      ],
            //    ],
        ],
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
    ],
];
```

- 执行数据库操作，生成表，命令如下：

```
php yii migrate --migrationPath=@yii/rbac/migrations/
```

- 控制访问，修改配置文件，比如 修改 `config/main.php`，添加：

```php
'components' => [
    // ...
],
'as access' => [
    'class' => 'yiier\rbac\components\AccessControl',
    // 'allowActions' => ['site/login', 'site/error', 'site/captcha', order/*] //白名单
],
```

### 可选操作

- 修改默认参数，修改 `config/params.php` 添加：

```php
'yiier.rbac.config' => [
    'cacheDuration' => 3600, // 缓存时间，默认是 30 天，单位是秒
    'superManId' => 12 //拥有所有权限
]
```


- 使用 rbac 模块的自带菜单

```php
echo  \yii\widgets\Menu::widget(
    [
        'options' => [
            'class' => 'sidebar-menu'
        ],
        'items' => [
            [
                'label' => Yii::t('app', 'Dashboard'),
                'url' => Yii::$app->homeUrl,
                'icon' => 'fa-dashboard',
                'active' => Yii::$app->request->url === Yii::$app->homeUrl
            ],
            \Yii::$app->getModule('rbac')->getInstance()->getItems() // add menu
        ]
    ]
);
```

- 使用 `\yiier\rbac\helpers\Html::a` ，判断是否有权限，如果没有权限自动隐藏链接

```php
<?= \yiier\rbac\helpers\Html::a(Yii::t('rbac', 'Clear Cache'), ['clear-cache']) ?>
```


- 使用 `yiier\rbac\widgets\Menu::widget` ，判断是否有权限，如果没有权限自动隐藏菜单中的链接


```php
echo  yiier\rbac\widgets\Menu::widget(
    [
        'options' => [
            'class' => 'sidebar-menu'
        ],
        'items' => [
            [
                'label' => Yii::t('app', 'Dashboard'),
                'url' => Yii::$app->homeUrl,
                'icon' => 'fa-dashboard',
                'active' => Yii::$app->request->url === Yii::$app->homeUrl
            ],
            \Yii::$app->getModule('rbac')->getInstance()->getItems() // add menu
        ]
    ]
);
```

- 使用 `yiier\rbac\widgets\SpuerMenu::widget` ，判断是否有权限，如果没有权限自动隐藏菜单中的链接，并且能实现自动高亮：

```php
echo  yiier\rbac\widgets\SpuerMenu::widget(
    [
        'options' => [
            'class' => 'sidebar-menu'
        ],
        'items' => [
            [
                'label' => Yii::t('app', 'Dashboard'),
                'url' => Yii::$app->homeUrl,
                'icon' => 'dashboard',
            ],
            [
                'label' => Yii::t('app', 'Create Product'), 
                'icon' => 'plus', 
                'url' => ['/product/create'], 
                'strict' => true
            ],
            \Yii::$app->getModule('rbac')->getInstance()->getItems() // add menu
        ]
    ]
);
```


### 界面操作教程

- 在『权限列表』中勾选你要控制的菜单，不勾选说明不打算加入权限控制

你也可以添加菜单的描述，更方便使用。描述会默认读取对应 `actionXXX` 方法中注释的 ` @title 权限列表`。

另外操作会自动立即保存，无需再点保存。

![](https://blog-1251237404.cos.ap-guangzhou.myqcloud.com/20190424225915.png)

- 在『角色管理』中，添加完角色之后再可以分别给角色分配用户和权限。操作的时候点击对应的权限/用户会立即自动保存。

![](https://blog-1251237404.cos.ap-guangzhou.myqcloud.com/20190424230424.png)

![](https://blog-1251237404.cos.ap-guangzhou.myqcloud.com/20190425181221.png)

![](https://blog-1251237404.cos.ap-guangzhou.myqcloud.com/20190425181059.png)

- 『用户列表』：只能看某个用户拥有的所有角色和所有权限

![](https://blog-1251237404.cos.ap-guangzhou.myqcloud.com/20190424230750.png)

- 『规则列表』：

待补充

> [Get√Yii](https://getyii.com/)
<i>Web development has never been so fun!</i>
[www.getyii.com](https://getyii.com/)