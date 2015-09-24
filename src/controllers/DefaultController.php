<?php

namespace yiier\rbac\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use  yiier\rbac\helpers\Files;
use yiier\rbac\Module;

/**
 * 角色管理主控制器
 */
class DefaultController extends Controller
{

    /**
     * @title 权限列表
     */
    public function actionIndex()
    {
        $actions = $this->_getBasePermission();
        return $this->render('index', ['classes' => $actions]);
    }

    /**
     * 创建许可
     * @title 创建许可
     */
    public function actionCreatePermission()
    {
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $permission = strtolower($request->get('permission'));
            $des = $request->get('des', '');
            $check = $request->get('check');
            if (empty($permission)) {
                return 0;
            }
            $auth = Yii::$app->authManager;
            if ($check === 'true') {
                $inDb = $auth->getPermission($permission);
                if ($inDb) {
                    $inDb->description = $des;
                    if ($auth->update($permission, $inDb)) {
                        $this->ajaxReturn(null, null, 1);
                    }
                } else {
                    $createPermission = $auth->createPermission($permission);
                    $createPermission->description = $des;
                    if ($auth->add($createPermission)) {
                        $this->ajaxReturn(null, null, 1);
                    }
                }

            } else {
                $per = $auth->getPermission($permission);
                if ($auth->remove($per)) {
                    $this->ajaxReturn(null, null, 1);
                }
            }
        }
    }

    /**
     * @title 取得权限
     */
    private function _getBasePermission()
    {
        $methods = $this->_getMethods();
        $permissions = [];
        foreach ($methods as $key => $val) {
            $module = explode('\\', $key);
            if (Yii::$app->getModule("{$module[1]}")) {
                $name = str_replace('\\', '_', str_replace("{$module[0]}\\", "{$module[0]}@", $key));
            } else {
                $name = str_replace('\\', '_', str_replace('\\modules\\', '@', $key));
            }
            $arr = explode('_', $name);
            foreach ($val as $k => $v) {
                $permissionName = $name . '_' . $k;
                $permission = $this->_isInDb($permissionName);

                $permissions[$arr[0]][$arr[1]][$permissionName] = [
                    'des' => $permission ? $permission->description : $v,
                    'action' => $k,
                    'check' => $permission,
                ];
            }
        }
        return $permissions;
    }

    /**
     * @title 取得方法
     */
    private function _getMethods()
    {
        // 在配置中添加的要接受控制的命名空间
        $namespaces = Module::getInstance()->allowNamespaces;
        // 不接受控制的 module
        $ignoreModules = Module::getInstance()->ignoreModules;

        if (!$namespaces) {
            $modules = Yii::$app->getModules();
            foreach ($modules as $k => $v) {
                if (!in_array($k, $ignoreModules)) {
                    $mod = Yii::$app->getModule($k);
                    $namespace = str_replace('/', '\\', $mod->controllerNamespace);
                    array_push($namespaces, $namespace);
                }
            }

            //当前所在命名空间的控制器
            $currentNamespace = str_replace('/', '\\', \Yii::$app->controllerNamespace);
            array_push($namespaces, $currentNamespace);
        }

        //获取类方法
        $actions = (new Files())->getAllMethods($namespaces);
        return $actions;
    }

    /**
     * 判断权限是否已经在库中
     * @param $permissions
     * @return bool|null|\yii\rbac\Permission
     */
    private function _isInDb($permissions)
    {
        if ($permission = Yii::$app->authManager->getPermission($permissions)) {
            return $permission;
        }
        return false;
    }
}