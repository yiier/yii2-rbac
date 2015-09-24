<?php

namespace yiier\rbac\components;

use yii\web\ForbiddenHttpException;
use Yii;
use yii\web\User;
use yii\di\Instance;

class AccessControl extends \yii\base\ActionFilter
{
    /**
     * @var User User for check access.
     */
    private $_user = 'user';

    /**
     * Get user
     * @return User
     */
    public function getUser()
    {
        if (!$this->_user instanceof User) {
            $this->_user = Instance::ensure($this->_user, User::className());
        }
        return $this->_user;
    }

    /**
     * Set user
     * @param User|string $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $user = $this->getUser();
        $controllerId = $action->controller->id;
        $moduleId = $action->controller->module->uniqueId ? '@' . $action->controller->module->uniqueId : '';

        $app = explode('\\', get_class(Yii::$app->controller));
        $permissionName = "{$app[0]}{$moduleId}_{$controllerId}_{$action->id}";
        if (Yii::$app->authManager->getPermission($permissionName)) {
            if (Yii::$app->user->can($permissionName)) {
                return true;
            }
            $this->denyAccess($user);
        }
        return true;
    }

    /**
     * @param $user
     * @throws ForbiddenHttpException
     */
    protected function denyAccess($user)
    {
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }

}