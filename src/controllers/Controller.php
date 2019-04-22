<?php

/**
 * @title 权限基类
 * @auth wsq cboy868@163.com
 */

namespace yiier\rbac\controllers;

use yii\filters\AccessControl;

class Controller extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            // 后台必须登录才能使用
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function ajaxReturn($success = true, $message = '', $data = '')
    {
        return [
            'status' => $success,
            'message' => $message,
            'data' => $data,
        ];
    }
}