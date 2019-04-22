<?php

namespace yiier\rbac\controllers;

use Yii;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yiier\rbac\helpers\Route;
use yiier\rbac\models\AuthRule;
use yiier\rbac\Module;

/**
 * 规则主控制器
 */
class RuleController extends Controller
{

    /**
     * @title 规则列表
     */
    public function actionIndex()
    {
        $rules = $this->getRules();
        return $this->render('index', ['rules' => $rules]);
    }

    /**
     * @title 创建规则
     * @return array|int
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $className = $request->get('class_name');
            $name = $request->get('name');
            $check = $request->get('check');
            if (empty($className)) {
                return $this->ajaxReturn(false, Yii::t('rbac', 'permission not found'));
            }
            $success = false;
            try {
                $dbRules = AuthRule::getRulesMap();
                if ($check === 'true') {
                    if (!empty($dbRules[$className])) {
                        $model = AuthRule::findOne($dbRules[$className]);
                    } else {
                        $model = new AuthRule();
                    }
                    $model->load(['name' => $name, 'className' => $className], '');
                    $success = $model->save();
                } else {
                    $success = AuthRule::findOne($name)->delete();
                }
                $message = Yii::t('rbac', 'successfully updated');
            } catch (\Exception $e) {
                $message = Yii::t('rbac', 'permission save error');
            }
            return $this->ajaxReturn($success, $message);
        }
        throw new NotFoundHttpException();
    }


    /**
     * 取得所有规则类
     */
    private function getRules()
    {
        // 规则所在的路径
        if (($rulesPath = Module::getInstance()->rulesPath) && is_array($rulesPath)) {
            $items = [];
            $dbRules = AuthRule::getRulesMap();
            foreach ($rulesPath as $key => $rulePath) {
                $files = FileHelper::findFiles(Yii::getAlias($rulePath), ['*.php']);
                foreach ((array)$files as $k => $file) {
                    $className = Route::getClassFromFile($file);
                    $id = "{$key}-{$k}";
                    $items[$id]['class_name'] = $className;
                    $items[$id]['name'] = isset($dbRules[$className]) ? $dbRules[$className] : '';
                    $items[$id]['check'] = !empty($dbRules[$className]);
                };
            }
            return array_values($items);
        }
        return [];
    }
}