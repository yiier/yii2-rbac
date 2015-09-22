<?php

namespace yiier\rbac\helpers;

use Yii;

class Files
{

    static $separate = '_';

    /**
     * @title 取得所有方法
     * @param $namespaces
     * @return
     */
    public static function getAllMethods($namespaces)
    {
        $namespaces = self::getClasses($namespaces);
        foreach ($namespaces as $k => $v) {
            if (!is_array($v)) {
                continue;
            }
            $namespace = $k . '\\controllers';

            $pre = strpos($k, '/') ? $k . self::$separate : $k . '/';
            foreach ($v as $key => $val) {
                $controller_namespace = $namespace . '\\' . $val . 'Controller';
                $val = self::uper2lower($val);
                $actions[$pre . $val] = self::getClassMethods($controller_namespace);
            }
        }
        return $actions;
    }

    /**
     * @title 取得某命名空间下的所有类
     * @param $namespaces
     * @return
     */
    public static function getClasses($namespaces)
    {
        foreach ($namespaces as $k => $v) {
            $namespace = str_replace('\\', '/', $v);
            $dir = Yii::getAlias('@' . $namespace);
            $key = str_replace('/controllers', '', $namespace);
            $classes[$key] = self::scan($dir);
        }
        return $classes;
    }

    /**
     * @title 取得一个类的所有公共方法
     * @param $controller
     * @return array
     */
    public static function getClassMethods($controller)
    {

        $actions = [];
        $controller = str_replace('/', '\\', $controller);
        $class = new \ReflectionClass($controller);//建立反射类
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        $filter = ['actions', 'behaviors'];
        foreach ($methods as $method) {
            if ($method->class == $controller && strpos($method->name, "action") === 0 && !in_array($method->name, $filter)) {
                preg_match('/\* @title(.*)/', $method->getDocComment(), $matches);
                $key = substr($method->name, 0, 6) == 'action' ? substr($method->name, 6) : $method->name;
                $key = self::uper2lower($key);
                $actions[$key] = isset($matches[1]) ? trim($matches[1]) : '';
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

    public static function uper2lower($action_id)
    {
        return ltrim(strtolower(preg_replace('/([A-Z])/', '-${1}', $action_id)), '-');
    }
}