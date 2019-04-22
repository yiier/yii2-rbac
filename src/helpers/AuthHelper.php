<?php
/**
 * author     : forecho <zhenghaicai@hk01.com>
 * createTime : 2019/4/21 11:15 AM
 * description:
 */

namespace yiier\rbac\helpers;


use Yii;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\IdentityInterface;

class AuthHelper
{
    const SESSION_PREFIX_LAST_UPDATE = '__auth_last_update';
    const SESSION_PREFIX_ROLES = '__userRoles';
    const SESSION_PREFIX_PERMISSIONS = '__userPermissions';
    const SESSION_PREFIX_ROUTES = '__userRoutes';

    /**
     * Hide link if user hasn't access to it
     *
     * @inheritdoc
     */
    public static function a($text, $url = null, $options = [])
    {
        if (in_array($url, [null, '', '#'])) {
            return Html::a($text, $url, $options);
        }
        return self::canRoute($url) ? Html::a($text, $url, $options) : '';
    }

    public static function canRoute($route)
    {
        $baseRoute = self::unifyRoute($route);
        if (substr($baseRoute, 0, 4) === "http") {
            return true;
        }
        AuthHelper::ensurePermissionsUpToDate();
//        pr(Yii::$app->session->get(self::SESSION_PREFIX_PERMISSIONS, []));
        return self::isRouteAllowed($baseRoute, Yii::$app->session->get(self::SESSION_PREFIX_PERMISSIONS, []));
    }


    /**
     * Gather all user permissions and roles and store them in the session
     *
     * @param IdentityInterface $identity
     */
    private static function updatePermissions($identity)
    {
        $session = Yii::$app->session;
        // Clear data first in case we want to refresh permissions
        $session->remove(self::SESSION_PREFIX_PERMISSIONS);

        // Set permissions last mod time
        $session->set(
            self::SESSION_PREFIX_LAST_UPDATE,
            filemtime(self::getPermissionsLastModFile())
        );

        $auth = Yii::$app->authManager;

        // Save roles, permissions and routes in session
        $session->set(
            self::SESSION_PREFIX_PERMISSIONS,
            array_keys($auth->getPermissionsByUser($identity->getId()))
        );
    }

    /**
     * @return string
     */
    public static function getPermissionName()
    {
        $controller = Yii::$app->controller;
        $controllerId = $controller->id;
        $actionId = $controller->action->id;
        $moduleId = $controller->module->uniqueId ? '@' . $controller->module->uniqueId : '';
        $app = explode('\\', get_class($controller));
        return "{$app[0]}{$moduleId}_{$controllerId}_{$actionId}";
    }

    /**
     * @param $route
     * @param $allowedRoutes
     * @return bool
     */
    public static function isRouteAllowed($route, $allowedRoutes)
    {
        $route = rtrim(Yii::$app->getRequest()->getBaseUrl(), '/') . $route;
        if (in_array($route, $allowedRoutes)) {
            return true;
        }
        foreach ($allowedRoutes as $allowedRoute) {
            // If some controller fully allowed (wildcard)
            if (substr($allowedRoute, -1) == '*') {
                $routeArray = explode('/', $route);
                array_splice($routeArray, -1);
                $allowedRouteArray = explode('/', $allowedRoute);
                array_splice($allowedRouteArray, -1);
                if (array_diff($routeArray, $allowedRouteArray) === array()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checks if permissions has been changed somehow, and refresh data in session if necessary
     */
    public static function ensurePermissionsUpToDate()
    {
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->session->get(self::SESSION_PREFIX_LAST_UPDATE) != filemtime(self::getPermissionsLastModFile())) {
                static::updatePermissions(Yii::$app->user->identity);
            }
        }
    }

    /**
     * Get path to file that store time of the last auth changes
     *
     * @return string
     */
    private static function getPermissionsLastModFile()
    {
        $file = Yii::$app->runtimePath . '/__permissions_last_mod.txt';
        if (!is_file($file)) {
            file_put_contents($file, '');
            chmod($file, 0777);
        }
        return $file;
    }

    /**
     * Change modification time of permissions last mod file
     */
    public static function invalidatePermissions()
    {
        touch(static::getPermissionsLastModFile());
    }

    /**
     * Return route without baseUrl and start it with slash
     *
     * @param string|array $route
     *
     * @return string
     */
    public static function unifyRoute($route)
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
                $routeAsString = substr_replace($routeAsString, '', 0,
                    strlen($baseUrl));
            }
        }
        $languagePrefix = '/' . Yii::$app->language . '/';
        // Remove language prefix
        if (strpos($routeAsString, $languagePrefix) === 0) {
            $routeAsString = substr_replace($routeAsString, '', 0,
                strlen($languagePrefix));
        }
        return '/' . ltrim($routeAsString, '/');
    }


    /**
     * @return array
     */
    public static function getAllModules()
    {
        $result = [];

        $currentEnvModules = \Yii::$app->getModules();

        foreach ($currentEnvModules as $moduleId => $uselessStuff) {
            $result[$moduleId] = \Yii::$app->getModule($moduleId);
        }

        return $result;
    }

    /**
     * @param \yii\base\Controller $controller
     * @param array $result all controller action.
     * @throws \ReflectionException
     */
    public static function getActionRoutes($controller, &$result)
    {
        $prefix = '/' . $controller->uniqueId . '/';
        foreach ($controller->actions() as $id => $value) {
            $result[] = $prefix . $id;
        }
        $class = new \ReflectionClass($controller);
        foreach ($class->getMethods() as $method) {
            $name = $method->getName();
            if ($method->isPublic() && !$method->isStatic() && strpos($name,
                    'action') === 0 && $name !== 'actions'
            ) {
                $result[] = $prefix . Inflector::camel2id(substr($name, 6));
            }
        }
    }
}