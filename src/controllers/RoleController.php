<?php

namespace yiier\rbac\controllers;

use Yii;
use yii\rbac\Role;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yiier\rbac\helpers\Pinyin;
use yiier\rbac\models\AuthItem;
use yiier\rbac\models\AuthItemSearch;


class RoleController extends Controller
{
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
     * 添加角色
     * @return mixed
     * @throws \Exception
     */
    public function actionCreate()
    {
        $model = new AuthItem();
        $model->type = AuthItem::TYPE_ROLE;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->createRole()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Create role success'));
                return $this->redirect('index');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 更新角色
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
                Yii::$app->session->setFlash('success', " '$model->name' " . Yii::t('app', 'successfully updated'));
                return $this->redirect(['view', 'name' => $name]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Displays a single AuthItem model.
     * @param string $name
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($name)
    {
        return $this->render('view', [
            'model' => $this->findRole($name),
        ]);
    }

    /**
     * @title 角色下的 所有用户
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
     * @title 分配用户角色
     */
    public function actionAssign()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $roleName = $request->post('role', '');
        $userId = $request->post('user_id', '');
        $isSel = $request->post('is_sel');

        $role = $this->auth->getRole($roleName);

        if (is_array($userId)) {
            foreach ($userId as $k => $v) {
                $this->auth->assign($role, $v);
            }
            return $this->ajaxReturn(null, null, 1);
        }

        if ($isSel == 'true') { //删除
            if ($this->auth->revoke($role, $userId)) {
                return $this->ajaxReturn(null, null, 1);
            }
        } else { //增加
            if ($this->auth->assign($role, $userId)) {
                return $this->ajaxReturn(null, null, 1);
            }
        }
    }

    /**
     * @title 角色下的 所有权限
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
            $arr = explode('_', $val->name);
            $new[$arr[0] . '_' . $arr[1]][] = [
                'name' => $val->name,
                'description' => $val->description,
                'is_sel' => in_array($val->name, $hasPermissions), // 是否拥有
            ];
        }

        return $this->render('permissions', ['permissions' => $new]);
    }


    /**
     * @title 分配用户权限
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

        if (is_array($id)) {
            foreach ($id as $k => $v) {
                $permissions = $auth->getPermission($v);
                $auth->addChild($role, $permissions);
            }
            return $this->ajaxReturn(null, null, 200);
        }

        $method = ($isSel == 'true') ? 'removeChild' : 'addChild';
        $permissions = $auth->getPermission($id);
        $auth->$method($role, $permissions);
        return $this->ajaxReturn(null, null, 200);
    }


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
        if ($this->auth->remove($role)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Delete success'));
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

    /**
     * @title 编辑角色
     * @param $roleName
     * @return array
     */
    public function actionEdit($roleName)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $role = $this->auth->getRole($roleName);
        if ($role) {
            return $this->ajaxReturn($role, null, 1);
        } else {
            return $this->ajaxReturn(null, '角色不存在', 0);
        }
    }
}