<?php

namespace yiier\rbac\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yiier\rbac\helpers\AuthHelper;
use yiier\rbac\helpers\Route;

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
//        $model = new \yiier\rbac\helpers\Route();
//        pr($model->getRoutes());
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
                return $this->ajaxReturn(false, Yii::t('rbac', 'permission not found'));
            }
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
                AuthHelper::invalidatePermissions();
                $message = Yii::t('rbac', 'successfully updated');
            } catch (\Exception $e) {
                $message = Yii::t('rbac', 'permission save error');
            }
            return $this->ajaxReturn($success, $message);
        }
        throw new NotFoundHttpException();
    }

    /**
     * 取得权限
     * @return array
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    private function getBasePermissions()
    {
        $methods = Route::getMethods();
        $permissions = [];
        foreach ($methods as $key => $val) {
            $arr = explode('@', $key);
            foreach ($val as $k => $v) {
                $appId = $arr[0] . '@';
                $permissionName = $appId . $k;
                $permission = Yii::$app->authManager->getPermission($permissionName);
                $permissions[$appId][$arr[1]][$permissionName] = [
                    'des' => $permission ? $permission->description : $v,
                    'rule_name' => $permission ? $permission->ruleName : "",
                    'action' => $k,
                    'check' => (bool)$permission,
                ];
            }
        }

        return $permissions;
    }
}