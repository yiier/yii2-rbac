<?php

namespace yiier\rbac\helpers;

use Yii;
use yii\caching\TagDependency;
use yiier\rbac\Module;

class Route
{
    const CACHE_TAG = 'yiier.rbac.route';

    /**
     * Get list of application routes
     * @return array
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public static function getAllRoutes()
    {
        $rbacModule = Module::getInstance();
        $key = [__METHOD__, Yii::$app->id, $rbacModule->getUniqueId()];
        $cache = Yii::$app->cache;
        if ($cache === null || ($result = $cache->get($key)) === false) {
            $result = self::getMethods();
            if ($cache !== null) {
                $cache->set($key, $result, $rbacModule->cacheDuration, new TagDependency([
                    'tags' => self::CACHE_TAG,
                ]));
            }
        }
        return $result;
    }

    /**
     * @title 取得方法
     * @return array
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    private static function getMethods()
    {
        $rbacModule = Module::getInstance();
        $actions = [];
        if ($rbacModule->isAdvanced && ($advancedConfigs = $rbacModule->advancedConfigs)) {
            $yiiApp = Yii::$app;
            foreach ($advancedConfigs as $configPaths) {
                $config = [];
                foreach ($configPaths as $configPath) {
                    // Merge every new configuration with the old config array.
                    $config = yii\helpers\ArrayHelper::merge($config, require(Yii::getAlias($configPath)));
                }
                unset($config['bootstrap']);
                $app = new yii\web\Application($config);
                $appId = Yii::$app->id . '@';
                $actions[$appId] = Route::getAllMethods(static::getNamespaces(), $appId);
                unset($app);
            }
            Yii::$app = $yiiApp;
            unset($yiiApp);
        } else {
            $appId = Yii::$app->id . '@';
            $actions = Route::getAllMethods(static::getNamespaces(), $appId);
        }

        return $actions;
    }


    /**
     * Get list of application routes
     * @return array
     */
    private static function getNamespaces()
    {
        $modules = Yii::$app->getModules();
        // 不接受控制的 module
        $ignoreModules = Module::getInstance()->ignoreModules;
        // 当前所在命名空间的控制器
        $namespaces = [str_replace('/', '\\', Yii::$app->controllerNamespace)];
        foreach ($modules as $id => $v) {
            if (!in_array($id, $ignoreModules)) {
                $namespaces = array_merge($namespaces, self::getNamespacesByModuleId($id));
            }
        }
        return $namespaces;
    }

    /**
     * @param $id
     * @return array
     */
    private static function getNamespacesByModuleId($id)
    {
        $namespaces = [];
        $mod = Yii::$app->getModule($id);
        $namespace = str_replace('/', '\\', $mod->controllerNamespace);
        array_push($namespaces, $namespace);
        return $namespaces;
    }


    /**
     * @title 取得所有方法
     * @param $namespaces
     * @param string $appId
     * @return array
     * @throws \ReflectionException
     */
    private static function getAllMethods($namespaces, $appId)
    {
        $actions = [];
        $namespaces = static::getClasses($namespaces);
        foreach ($namespaces as $namespace => $classNames) {
            if (is_array($classNames)) {
                $controllers = $namespace . '\\controllers';
                $firstPrefix = '';
                if (strpos($namespace, '\\') !== false) {
                    $prefixArray = explode('\\', $namespace);
                    $firstPrefix = end($prefixArray) . '/';
                }
                foreach ($classNames as $key => $className) {
                    $controllerNamespace = $controllers . '\\' . $className . 'Controller';
                    $className = self::uper2lower($className);
                    $prefix = $firstPrefix . $className;
                    $classMethods = self::getClassMethods($controllerNamespace, $prefix);
                    $classMethods ? $actions[$appId . $firstPrefix . $className] = $classMethods : null;
                }
            }
        }
        return $actions;
    }

    /**
     * @title 取得某命名空间下的所有类
     * @param $namespaces
     * @return array
     */
    private static function getClasses($namespaces)
    {
        $classes = [];
        foreach ($namespaces as $k => $v) {
            $key = str_replace('\controllers', '', $v);
            $namespace = str_replace('\\', '/', $v);
            $dir = Yii::getAlias('@' . $namespace);
            $classes[$key] = self::scan($dir);
        }
        return $classes;
    }

    /**
     * @title 取得一个类的所有公共方法
     * @param string $controller
     * @param string $prefix
     * @return array
     * @throws \ReflectionException
     */
    private static function getClassMethods($controller, $prefix)
    {
        $actions = [];
        $controller = str_replace('/', '\\', $controller);
        $class = new \ReflectionClass($controller);//建立反射类
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $name = $method->getName();
            if ($method->class == $controller &&
                !$method->isStatic() &&
                strpos($name, 'action') === 0 &&
                $name !== 'actions'
            ) {
                preg_match('/\* @title(.*)/', $method->getDocComment(), $matches);
                $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', substr($name, 6)));
                $id = "/{$prefix}/" . ltrim(str_replace(' ', '-', $name), '-');
                $actions[$id] = isset($matches[1]) ? trim($matches[1]) : '';
            }
        }
        return $actions;
    }

    /**
     * @title 扫描目录文件
     * @param $dir
     * @return array
     */
    private static function scan($dir)
    {
        $classes = array_diff(\scandir($dir), ['.', '..']);
        foreach ($classes as $k => &$v) {
            if (substr($v, -14) != 'Controller.php') {
                unset($classes[$k]);
            }
            $v = substr($v, 0, -14);
        }
        unset($v);
        return $classes;
    }

    private static function uper2lower($actionId)
    {
        return ltrim(strtolower(preg_replace('/([A-Z])/', '-${1}', $actionId)), '-');
    }


    /**
     * get php file className
     * @param $filename
     * @return string
     */
    public static function getClassFromFile($filename)
    {
        $lines = file($filename);
        $namespaces = preg_grep('/^namespace /', $lines);
        $namespaceLine = array_shift($namespaces);
        $match = [];
        preg_match('/^namespace (.*);$/', $namespaceLine, $match);
        $fullNamespace = array_pop($match);

        $directoriesAndFilename = explode('/', $filename);
        $filename = array_pop($directoriesAndFilename);
        $nameAndExtension = explode('.', $filename);
        $className = array_shift($nameAndExtension);
        return $fullNamespace . '\\' . $className;
    }


    /**
     * invalidate cache
     */
    public static function invalidate()
    {
        if (Yii::$app->cache !== null) {
            TagDependency::invalidate(Yii::$app->cache, self::CACHE_TAG);
        }
    }
}