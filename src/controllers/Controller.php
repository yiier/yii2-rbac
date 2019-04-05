<?php

/**
 * @title 权限基类
 * @auth wsq cboy868@163.com
 */

namespace yiier\rbac\controllers;

use Yii;
use yii\web\BadRequestHttpException;

class Controller extends \yii\web\Controller
{
    public function init()
    {
        //判断是否登录
        if (Yii::$app->user->isGuest) {
            $this->redirect(Yii::$app->user->loginUrl);
        }
    }

    public function ajaxReturn($data = null, $info = '', $success = true)
    {
        return [
            'status' => $success,
            'info' => $info,
            'data' => $data,
            'csrf' => Yii::$app->request->getCsrfToken()
        ];
    }

    /**
     * @title 权限验证
     * @param \yii\base\Action $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $ac = $this->getFullAction($action);
            $auth = Yii::$app->authManager;

            //判断是否是普通用户，如果是，跳到前台
            $role = $auth->getRolesByUser(Yii::$app->user->getId());
            $customerRole = $auth->getRole('customer');
            if (isset($role['customer']) && $role['customer'] === $customerRole) {
                Yii::$app->getSession()->setFlash('error', '非法操作');
                $this->redirect('/');
            }

            if (($auth->getPermission($ac) && !\Yii::$app->user->can($ac)) && \Yii::$app->user->id != 1) {
                throw new BadRequestHttpException(Yii::t('yii', '您无权进行此操作'));
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @title 取得权限全名
     * @param $action
     * @return string
     */
    private function getFullAction($action)
    {
        $namespace = str_replace('\controllers', '', \Yii::$app->controllerNamespace);
        $mod = \Yii::$app->controller->module !== null ? '@' . \Yii::$app->controller->module->id : "";
        $controller = \Yii::$app->controller->id;
        $ac = $action->id;
        return $namespace . $mod . '-' . $controller . '-' . $ac;
    }
}