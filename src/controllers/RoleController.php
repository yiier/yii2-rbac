<?php

namespace yiier\rbac\controllers;

use Yii;
use yii\rbac\Role;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yiier\rbac\helpers\AuthHelper;
use yiier\rbac\helpers\Pinyin;
use yiier\rbac\models\AuthItem;
use yiier\rbac\models\AuthItemSearch;
use yiier\rbac\Module;


class RoleController extends Controller
{
    /**
     * @var  \yii\rbac\ManagerInterface
     */
    public $auth;

    public function init()
    {
        parent::init();
        $this->auth = Yii::$app->authManager;
    }

    /**
     * @title 权限列表
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get(), AuthItem::TYPE_ROLE);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @title 添加角色
     * @return mixed
     * @throws \Exception
     */
    public function actionCreate()
    {
        $model = new AuthItem();
        $model->type = AuthItem::TYPE_ROLE;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->createRole()) {
                Yii::$app->session->setFlash('success', Yii::t('rbac', 'Create role success'));
                return $this->redirect('index');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @title 更新角色
     * @param $name
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($name)
    {
        $model = $this->findModel($name);
        $model->type = AuthItem::TYPE_ROLE;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->updateRole($name)) {
                Yii::$app->session->setFlash('success', " '$model->name' " . Yii::t('rbac', 'successfully updated'));
                return $this->redirect('index');
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * @title 管理角色下的所有用户
     * @param $name
     * @return string
     */
    public function actionUser($name)
    {
        $users = (new UserController('user', Yii::$app->module))->findAll();
        $usersInfo = [];
        foreach ($users as $key => $value) {
            $pin = strtoupper(substr(Pinyin::pinyin($value), 0, 1));
            $usersInfo[$pin][$key] = [
                'username' => $value,
                'pinyin' => Pinyin::pinyin($value),
                'is_sel' => $this->auth->getAssignment($name, $key) ? 1 : 0
            ];
        }

        return $this->render('user', ['user' => $usersInfo]);
    }

    /**
     * @title 给角色分配用户
     */
    public function actionAssign()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $roleName = $request->post('role', '');
        $userId = $request->post('user_id', '');
        $isSel = $request->post('is_sel');

        $role = $this->auth->getRole($roleName);

        AuthHelper::invalidate();
        if (is_array($userId)) {
            foreach ($userId as $k => $v) {
                $this->auth->assign($role, $v);
            }
            return $this->ajaxReturn();
        }

        if ($isSel == 'true') { //删除
            if ($this->auth->revoke($role, $userId)) {
                return $this->ajaxReturn();
            }
        } else { //增加
            if ($this->auth->assign($role, $userId)) {
                return $this->ajaxReturn();
            }
        }
    }

    /**
     * @title 管理角色下的所有权限
     * @param $name
     * @return string
     */
    public function actionPermissions($name)
    {
        $auth = Yii::$app->authManager;
        $permissions = $auth->getPermissions();
        $hasPermissions = array_keys($auth->getPermissionsByRole($name));
        $new = [];
        foreach ($permissions as $key => $val) {
            $newKey = substr($val->name, 0, strripos($val->name, '/'));
            $new[$newKey][] = [
                'name' => $val->name,
                'description' => $val->description,
                'is_sel' => in_array($val->name, $hasPermissions), // 是否拥有
            ];
        }

        return $this->render('permissions', ['permissions' => $new]);
    }


    /**
     * @title 给角色分配权限
     */
    public function actionAssignPermissions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $auth = Yii::$app->authManager;

        $request = Yii::$app->request;
        $roleName = $request->post('role', '');
        $isSel = $request->post('is_sel');
        $id = $request->post('id', '');

        $role = $auth->getRole($roleName);
        AuthHelper::invalidate();
        if (is_array($id)) {
            foreach ($id as $k => $v) {
                $permissions = $auth->getPermission($v);
                $auth->addChild($role, $permissions);
            }
            return $this->ajaxReturn();
        }

        $method = ($isSel == 'true') ? 'removeChild' : 'addChild';
        $permissions = $auth->getPermission($id);
        $auth->$method($role, $permissions);
        return $this->ajaxReturn();
    }

    /**
     * @param $name
     * @return AuthItem
     * @throws NotFoundHttpException
     */
    protected function findModel($name)
    {
        if ($name) {
            $model = new AuthItem();
            $role = $this->findRole($name);
            $model->name = $role->name;
            $model->description = $role->description;
            $model->setIsNewRecord(false);
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @title 删除角色
     * @param $name
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($name)
    {
        $role = $this->findRole($name);
        if (Module::getInstance()->safeDelete && $this->auth->getUserIdsByRole($name)) {
            Yii::$app->session->setFlash(
                'error',
                Yii::t('rbac', 'Delete role error, There are already users under this role.')
            );
        } elseif ($this->auth->remove($role)) {
            Yii::$app->session->setFlash('success', Yii::t('rbac', 'Delete success'));
        }
        return $this->redirect(['index']);
    }

    /**
     * 查找角色
     * @param $name
     * @return null|Role
     * @throws NotFoundHttpException
     */
    protected function findRole($name)
    {
        if (($role = Yii::$app->getAuthManager()->getRole($name)) !== null) {
            return $role;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}