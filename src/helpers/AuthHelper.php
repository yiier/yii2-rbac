<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019/4/21 11:15 AM
 * description:
 */

namespace yiier\rbac\helpers;


use Yii;
use yii\caching\TagDependency;
use yii\helpers\Url;
use yii\web\User;

class AuthHelper
{
    private static $_userRoutes = [];
    private static $_allPermission = [];

    /**
     * @param $route
     * @param User|null $user
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function canRoute($route, User $user = null)
    {
        $userId = $user ? $user->getId() : Yii::$app->getUser()->getId();
        if ($userId == Config::instance()->superManId) {
            return true;
        }

        $baseRoute = self::unifyRoute($route);
        if (substr($baseRoute, 0, 4) === "http") {
            return true;
        }
        $routesByUser = static::getRoutesByUser($userId);
        return self::isRouteAllowed($baseRoute, $routesByUser);
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    private static function getAllPermissions()
    {
        if (!self::$_allPermission) {
            $cache = Yii::$app->cache;
            if ($cache && ($routes = $cache->get([__METHOD__])) !== false) {
                self::$_allPermission = $routes;
            } else {
                $routes = [];
                $manager = Yii::$app->authManager;
                foreach ($manager->getPermissions() as $item) {
                    $routes[] = $item->name;
                }
                self::$_allPermission = $routes;
                if ($cache) {
                    $cache->set(
                        [__METHOD__],
                        $routes,
                        Config::instance()->cacheDuration,
                        new TagDependency(['tags' => Config::CACHE_TAG])
                    );
                }
            }
        }
        return self::$_allPermission;
    }

    /**
     * @param $userId
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    private static function getRoutesByUser($userId)
    {
        if (!isset(self::$_userRoutes[$userId])) {
            $cache = Yii::$app->cache;
            if ($cache && ($routes = $cache->get([__METHOD__, $userId])) !== false) {
                self::$_userRoutes[$userId] = $routes;
            } else {
                $routes = [];
                $manager = Yii::$app->authManager;
                foreach ($manager->getPermissionsByUser($userId) as $item) {
                    $routes[] = $item->name;
                }
                self::$_userRoutes[$userId] = $routes;
                if ($cache) {
                    $cache->set(
                        [__METHOD__, $userId], $routes,
                        Config::instance()->cacheDuration,
                        new TagDependency(['tags' => Config::CACHE_TAG])
                    );
                }
            }
        }
        return self::$_userRoutes[$userId];
    }

    /**
     * @param $route
     * @param $allowedRoutes
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    private static function isRouteAllowed($route, $allowedRoutes)
    {
        $route = rtrim(Yii::$app->getRequest()->getBaseUrl(), '/') . $route;
        $permission = Yii::$app->id . '@' . $route;
        // 必选先在『权限列表』页面勾选权限，才会去判断权限权限
        if (!in_array($permission, self::getAllPermissions())) {
            return true;
        }

        if (in_array($permission, $allowedRoutes)) {
            return true;
        }
        return false;
    }

    /**
     * Return route without baseUrl and start it with slash
     *
     * @param string|array $route
     *
     * @return string
     */
    private static function unifyRoute($route)
    {
        // If its like Html::a('Create', ['create'])
        if (is_array($route) AND strpos($route[0], '/') === false) {
            $route = Url::toRoute($route);
        }
        // URL starts with http
        if (!is_array($route) && (substr($route, 0, 4) === "http")) {
            return $route;
        }
        if (Yii::$app->getUrlManager()->showScriptName === true) {
            $baseUrl = Yii::$app->getRequest()->scriptUrl;
        } else {
            $baseUrl = Yii::$app->getRequest()->baseUrl;
        }
        // Check if $route has been passed as array or as string with params (or without)
        if (!is_array($route)) {
            $route = explode('?', $route);
        }
        $routeAsString = $route[0];
        // If it's not clean url like localhost/folder/index.php/bla-bla then remove
        // baseUrl and leave only relative path 'bla-bla'
        if ($baseUrl) {
            if (strpos($routeAsString, $baseUrl) === 0) {
                $routeAsString = substr_replace($routeAsString, '', 0, strlen($baseUrl));
            }
        }
        $languagePrefix = '/' . Yii::$app->language . '/';
        // Remove language prefix
        if (strpos($routeAsString, $languagePrefix) === 0) {
            $routeAsString = substr_replace($routeAsString, '', 0, strlen($languagePrefix));
        }
        return '/' . ltrim($routeAsString, '/');
    }

    /**
     * Use to invalidate cache.
     */
    public static function invalidate()
    {
        if (Yii::$app->cache !== null) {
            TagDependency::invalidate(Yii::$app->cache, Config::CACHE_TAG);
        }
    }
}