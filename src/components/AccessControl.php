<?php

namespace yiier\rbac\components;

use Yii;
use yii\di\Instance;
use yii\web\ForbiddenHttpException;
use yii\web\User;
use yiier\rbac\helpers\AuthHelper;

class AccessControl extends \yii\base\ActionFilter
{
    /**
     * @var User User for check access.
     */
    private $_user = 'user';

    /**
     * Get user
     * @return User
     * @throws \yii\base\InvalidConfigException
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
     * @throws \yii\base\InvalidConfigException
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        $user = $this->getUser();
        if (AuthHelper::canRoute(Yii::$app->getRequest()->url, $user)) {
            return true;
        }
        $this->denyAccess($user);
    }

    /**
     * @param User $user
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

    /**
     * @inheritdoc
     */
    protected function isActive($action)
    {
        $uniqueId = $action->getUniqueId();
        if ($uniqueId === Yii::$app->getErrorHandler()->errorAction) {
            return false;
        }
        return true;
    }
}