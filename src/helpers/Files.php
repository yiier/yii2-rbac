<?php

namespace yiier\rbac\helpers;

use Yii;

class Files
{

    static $separate = '_';

    /**
     * @title 取得所有方法
     * @param $namespaces
     * @return array
     * @throws \ReflectionException
     */
    public function getAllMethods($namespaces)
    {
        $actions = [];
        $namespaces = $this->getClasses($namespaces);
        foreach ($namespaces as $k => $v) {
            if (is_array($v)) {
                $namespace = $k . '\\controllers';
                $firstPrefix = '';
                if (strpos($k, '\\') !== false) {
                    $prefixArray = explode('\\', $k);
                    $firstPrefix = end($prefixArray) . '/';
                }
                foreach ($v as $key => $val) {
                    $controllerNamespace = $namespace . '\\' . $val . 'Controller';
                    $val = self::uper2lower($val);
                    $prefix = $firstPrefix . $val;
                    $classMethods = self::getClassMethods($controllerNamespace, $prefix);
                    $classMethods ? $actions[$k . '\\' . $val] = $classMethods : null;
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
    private function getClasses($namespaces)
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
    public static function getClassMethods($controller, $prefix)
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
//                $key = substr($method->name, 0, 6) == 'action' ? substr($method->name, 6) : $method->name;
//                $key = self::uper2lower($key);
//                $actions[$key] = isset($matches[1]) ? trim($matches[1]) : '';
            }
        }
        return $actions;
    }

    /**
     * @title 扫描目录文件
     * @param $dir
     * @return array
     */
    public static function scan($dir)
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

    public static function uper2lower($actionId)
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
}