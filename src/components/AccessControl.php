<?php

namespace yiier\rbac\components;

use Yii;
use yii\di\Instance;
use yii\web\ForbiddenHttpException;
use yii\web\User;
use yiier\rbac\helpers\AuthHelper;

/**
 * Access Control Filter (ACF) is a simple authorization method that is best used by applications that only need some simple access control.
 * As its name indicates, ACF is an action filter that can be attached to a controller or a module as a behavior.
 * ACF will check a set of access rules to make sure the current user can access the requested action.
 *
 * To use AccessControl, declare it in the application config as behavior.
 * For example.
 *
 * ```
 * 'as access' => [
 *     'class' => 'yiier\rbac\components\AccessControl',
 * ]
 * ```
 *
 * @property User $user
 *
 * @author forecho <caizhenghai@gmail.com>
 * @since 1.0
 */
class AccessControl extends \yii\base\ActionFilter
{
    /**
     * @var array List of action that not need to check access.
     */
    public $allowActions = [];

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
        if ((!$user->getIsGuest()) && AuthHelper::canRoute(Yii::$app->getRequest()->url, $user, $action)) {
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
     * @param \yii\base\Action $action
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    protected function isActive($action)
    {
        $uniqueId = $action->getUniqueId();
        if ($uniqueId === Yii::$app->getErrorHandler()->errorAction) {
            return false;
        }

        $user = $this->getUser();
        if ($user->getIsGuest()) {
            $loginUrl = null;
            if (is_array($user->loginUrl) && isset($user->loginUrl[0])) {
                $loginUrl = $user->loginUrl[0];
            } else {
                if (is_string($user->loginUrl)) {
                    $loginUrl = $user->loginUrl;
                }
            }
            if (!is_null($loginUrl) && trim($loginUrl, '/') === $uniqueId) {
                return false;
            }
        }
        return true;
    }
}