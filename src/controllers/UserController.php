<?php

namespace yiier\rbac\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\rbac\Assignment;
use yii\web\NotFoundHttpException;
use yiier\rbac\models\UserSearch;
use yiier\rbac\Module;

class UserController extends Controller
{
    public $userClassName;
    public $idField = 'id';
    public $usernameField = 'username';
    public $searchClass;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->searchClass = Module::getInstance()->searchClass;
        $this->idField = Module::getInstance()->idField;
        $this->usernameField = Module::getInstance()->usernameField;
        $this->userClassName = Module::getInstance()->userClassName;

        if ($this->userClassName === null) {
            $this->userClassName = Yii::$app->getUser()->identityClass;
            $this->userClassName = $this->userClassName ?: 'common\models\User';
        }
    }

    /**
     * Lists all Assignment models.
     * @return mixed
     */
    public function actionIndex()
    {

        if ($this->searchClass === null) {
            $searchModel = new UserSearch();
        } else {
            $class = $this->searchClass;
            $searchModel = new $class;
        }

        $dataProvider = $searchModel->search(
            \Yii::$app->request->getQueryParams(),
            $this->userClassName,
            $this->usernameField,
            $this->idField
        );

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'idField' => $this->idField,
            'usernameField' => $this->usernameField,
        ]);
    }

    /**
     * Displays a single Assignment model.
     * @param  integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $permissions = Yii::$app->authManager->getPermissionsByUser($id);

        $new = [];
        foreach ($permissions as $key => $val) {
            $arr = explode('_', $val->name);
            $new[$arr[0] . '_' . $arr[1]][] = [
                'name' => $val->name,
                'description' => $val->description,
            ];
        }

        return $this->render('view', [
            'model' => $model,
            'idField' => $this->idField,
            'permissions' => $new,
            'usernameField' => $this->usernameField,
        ]);
    }

    /**
     * Finds the Assignment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  integer $id
     * @return Assignment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $class = $this->userClassName;
        if (($model = $class::findIdentity($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function findAll()
    {
        $class = $this->userClassName;
        return ArrayHelper::map($class::find()->all(), 'id', $this->usernameField);
    }
}