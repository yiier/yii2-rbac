<?php

namespace yiier\rbac\models;

use common\models\AuthTimes;
use Yii;
use yii\base\Exception;

class AuthItem extends \yii\db\ActiveRecord
{
    /**
     * Auth type
     */
    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;
    public $ruleName;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            ['name', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            ['name', 'validatePermission'],
            ['ruleName', 'safe'],
        ];
    }

    public function validatePermission($attribute)
    {
        if (!$this->hasErrors()) {
            $auth = Yii::$app->getAuthManager();
            $labels = $this->attributeLabels();
            if ($this->isNewRecord && $auth->getPermission($this->name)) {
                $this->addError('name', $labels[$attribute] . Yii::t('app', 'already exists.'));
            }
            if ($this->isNewRecord && $auth->getRole($this->name)) {
                $this->addError('name', $labels[$attribute] . Yii::t('app', 'already exists.'));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Keyword'),
            'type' => Yii::t('app', 'Type'),
            'description' => Yii::t('app', 'Role Name'),
            'rule_name' => Yii::t('app', 'Rule Name'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * 次数
     * @return \yii\db\ActiveQuery
     */
    public function getAuthTimes()
    {
        return $this->hasOne(AuthTimes::className(), ['name' => 'name',])
            ->onCondition([AuthTimes::tableName() . '.type' => 1]);
    }

    /**
     * 查找角色
     * @param $name
     * @return bool|void
     */
    public function findRole($name)
    {
        if (($role = Yii::$app->getAuthManager()->getRole($name)) !== null) {
            return $role;
        }
        return false;
    }

    /**
     * 添加角色
     * @return bool
     * @throws Exception
     */
    public function createRole()
    {
        if ($this->validate()) {
            $auth = Yii::$app->getAuthManager();
            $role = $auth->createRole($this->name);
            $role->description = $this->description;
            !empty($this->rule_name) && $role->ruleName = $this->rule_name;
            $role->data = $this->data;

            if ($auth->add($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 更新角色
     * @param $name
     * @return bool
     * @throws Exception
     */
    public function updateRole($name)
    {
        if ($this->validate()) {
            $auth = Yii::$app->getAuthManager();
            $role = $auth->getRole($name);
            $role->description = $this->description;
            !empty($this->rule_name) && $role->ruleName = $this->rule_name;
            $role->data = $this->data;

            if ($auth->update($name, $role)) {
                return true;
            }
        }
        return false;
    }
}
