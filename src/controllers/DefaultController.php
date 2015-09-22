<?php 

namespace yiier\rbac\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use  yiier\rbac\helpers\Files;
/**
 * 角色管理主控制器
 */
class DefaultController extends Controller
{

	/**
	 * @title 权限列表
	 */
	public function actionIndex()
	{
		$actions = $this->_getBasePermission();
		return $this->render('index', ['classes'=>$actions]);
	}

	/**
	 * 创建许可
	 * @title 创建许可
	 */
	public function actionCreatePermission()
	{
		if (Yii::$app->request->isAjax) {
			$request = Yii::$app->request;
			$permision = strtolower($request->get('permission'));
			$des = $request->get('des', '');
			$check = $request->get('check');
			if (empty($permision)) {
				return 0;
			}
			// p($check);die;
			$auth = Yii::$app->authManager;

	        if ($check==='true') {
	        	$inDb = $auth->getPermission($permision);
	        	if ($inDb) {
	        		$inDb->description = $des;
	        		if ($auth->update($permision, $inDb)) {
	        			$this->ajaxReturn(null, null, 1);
	        		}
	        	} else {
	        		$createPermission = $auth->createPermission($permision);
		        	$createPermission->description = $des;
		        	if ($auth->add($createPermission)) {
			        	$this->ajaxReturn(null, null, 1);
			        }
	        	}
	        	
	        } else {
	        	$per = $auth->getPermission($permision);
	        	if ($auth->remove($per)) {
		        	$this->ajaxReturn(null, null, 1);
		        }
	        }
		}
	}
	/**
	 * @title 取得权限
	 */
	private function _getBasePermission()
	{
		$methods = $this->_getMethods();
		$permisions = [];
		foreach ($methods as $key => $val) {
			$name = str_replace('/', '_', str_replace('/modules/', '@', $key));
			$arr = explode('_', $name);

			foreach ($val as $k => $v) {
				$permisions[$arr[0]][$arr[1]][$name.'_'.$k] = [
					'des' => $v,
					'action' => $k
				];
			}
		}
		return $this->_isInDb($permisions);
	}

	/**
	 * @title 取得方法
	 */
	private function _getMethods()
	{
		$module = \Yii::$app->controller->module;

		//在配置中添加的要接受控制的命名空间
//		$namespaces = $module->params['rbacPath'];
		$namespaces = [];

		//不要接受控制的 module
		$sys_module = ['debug', 'gii'];

		$modules = Yii::$app->getModules();
		foreach ($modules as $k => $v) {
			if (in_array($k, $sys_module)) {
				continue;
			}
			$mod = Yii::$app->getModule($k);
			$namespace = str_replace('/', '\\', $mod->controllerNamespace);
			array_push($namespaces, $namespace);
		}

		//当前所在命名空间的控制器
		$currentNamespace = str_replace('/', '\\', \Yii::$app->controllerNamespace);
		array_push($namespaces, $currentNamespace);

		//获取类方法
		$actions = Files::getAllMethods($namespaces);
		return $actions;
	}

	/**
	 * 判断权限是否已经在库中
	 * @param $control_actions
	 * @return
	 */
	private function _isInDb($control_actions)
	{
		$auth = Yii::$app->authManager;
		$model_actions = $auth->getPermissions();

		$action_k_v = ArrayHelper::getColumn($model_actions, 'description');
		foreach ($control_actions as $k => $value) {
			foreach ($value as $key => $val) {
				foreach ($val as $ac=>$v) {
					if (array_key_exists(strtolower($ac), $action_k_v) !== false) {
						$control_actions[$k][$key][$ac]['check']=true;
						!empty($action_k_v[$ac]) && 
							$control_actions[$k][$key][$ac]['des'] = $action_k_v[$ac];
					}
				}
			}
		}
		return $control_actions;
	}
}