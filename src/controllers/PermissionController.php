<?php

namespace yiier\rbac\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yiier\rbac\helpers\Files;
use yiier\rbac\Module;

/**
 * 角色管理主控制器
 */
class PermissionController extends Controller
{

    /**
     * @title 权限列表
     * @return string
     * @throws \ReflectionException
     */
    public function actionIndex()
    {
        $basePermissions = $this->getBasePermissions();
        return $this->render('index', ['basePermissions' => $basePermissions]);
    }

    /**
     * @title 创建权限
     * @return array|int
     * @throws \Exception
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $permission = strtolower($request->get('permission'));
            $des = $request->get('des');
            $ruleName = $request->get('rule_name') ? $request->get('rule_name') : null;
            $check = $request->get('check');
            if (empty($permission)) {
                return $this->ajaxReturn(null, Yii::t('rbac', 'permission not found'), false);
            }
            $message = Yii::t('rbac', 'permission save error');
            $success = false;
            try {
                $auth = Yii::$app->authManager;
                if ($check === 'true') {
                    if ($inDb = $auth->getPermission($permission)) {
                        $inDb->description = $des;
                        $inDb->ruleName = $ruleName;
                        $success = $auth->update($permission, $inDb);
                    } else {
                        $createPermission = $auth->createPermission($permission);
                        $createPermission->description = $des;
                        $createPermission->ruleName = $ruleName;
                        $success = $auth->add($createPermission);
                    }
                } else {
                    $per = $auth->getPermission($permission);
                    $success = $auth->remove($per);
                }
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }
            return $this->ajaxReturn(null, $message, $success);
        }
        throw new NotFoundHttpException();
    }

    /**
     * @title 取得权限
     * @return array
     * @throws \ReflectionException
     */
    private function getBasePermissions()
    {
        $methods = $this->getMethods();
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
                $permission = Yii::$app->authManager->getPermission($permissionName);
                $permissions[$arr[0]][$arr[1]][$permissionName] = [
                    'des' => $permission ? $permission->description : $v,
                    'rule_name' => $permission ? $permission->ruleName : "",
                    'action' => $k,
                    'check' => (bool)$permission,
                ];
            }
        }
        return $permissions;
    }

    /**
     * @title 取得方法
     * @return array
     * @throws \ReflectionException
     */
    private function getMethods()
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
}