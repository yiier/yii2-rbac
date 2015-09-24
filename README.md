RBAC Manager for Yii 2
==============
RBAC Auth manager for Yii2

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

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
    'modules' => [
        'rbac' => [
            'class' => 'yiier\rbac\Module',
            // 'allowNamespaces' => [
            //    'yiier\rbac\controllers',
            //    'api\controllers'
            // ],
            // 'menus' => [
            //    'users' => 'User'
            // ],
            // 'userClassName' => 'app\models\User',
            // 'idField' => 'id',
            // 'usernameField' => 'shop_name',
        ],
    ],
    'components' => [

        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
    ],
    'as access' => [
        'class' => 'yiier\rbac\components\AccessControl',
    ],
];
```

**use menu**

```php
echo  Menu::widget(
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
            \Yii::$app->getModule('rbac')->getInstance()->getItems()
        ]
    ]
);
```