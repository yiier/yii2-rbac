<?php

namespace yiier\rbac\models;

use Yii;

/**
 * This is the model class for table "{{%auth_item}}".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 *
 *
 * @author forecho <caizhenghai@gmail.com>
 */
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
        return '{{%auth_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            ['name', 'unique'],
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
                $this->addError('name', $labels[$attribute] . Yii::t('rbac', 'already exists.'));
            }
            if ($this->isNewRecord && $auth->getRole($this->name)) {
                $this->addError('name', $labels[$attribute] . Yii::t('rbac', 'already exists.'));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('rbac', 'Role Name'),
            'type' => Yii::t('rbac', 'Type'),
            'description' => Yii::t('rbac', 'Description'),
            'rule_name' => Yii::t('rbac', 'Rule Name'),
            'data' => Yii::t('rbac', 'Data'),
            'created_at' => Yii::t('rbac', 'Created At'),
            'updated_at' => Yii::t('rbac', 'Updated At'),
        ];
    }


    /**
     * 添加角色
     * @return bool
     * @throws \Exception
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
     * @throws \Exception
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
