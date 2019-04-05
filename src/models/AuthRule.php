<?php
/**
 * author     : forecho <zhenghaicai@hk01.com>
 * createTime : 2019/4/5 5:26 PM
 * description:
 */

namespace yiier\rbac\models;

use yii\behaviors\TimestampBehavior;
use yii\rbac\Rule;

/**
 * This is the model class for table "{{%auth_rule}}".
 *
 * @property string $name
 * @property string $data
 * @property int $created_at
 * @property int $updated_at
 *
 * @property AuthItem[] $sysAuthItems
 */
class AuthRule extends \yii\db\ActiveRecord
{
    /**
     * @var string RBAC 规则类名
     */
    public $className;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%auth_rule}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'className'], 'required'],
            [['name'], 'required'],
            [['data'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['name'], 'unique'],
            [['className'], 'classExists']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => \Yii::t('rbac', 'Rule Name'),
            'className' => \Yii::t('rbac', 'Rule Class Name'),
            'data' => \Yii::t('rbac', 'Rule Data'),
            'created_at' => \Yii::t('rbac', 'Created At'),
            'updated_at' => \Yii::t('rbac', 'Updated At'),
        ];
    }

    /**
     * 验证类名称是否符合规则
     */
    public function classExists()
    {
        if (!class_exists($this->className)) {
            $message = "not fount '{$this->className}'";
            $this->addError('className', \Yii::t('rbac', $message));
        }

        if (!is_subclass_of($this->className, Rule::class)) {
            $message = "'{$this->className}' must is RBAC rule class";
            $this->addError('className', \Yii::t('rbac', $message));
        }
    }

    /**
     * @param $data
     * @return bool|string
     */
    public static function getClassName($data)
    {
        if (!empty($data)) {
            $data = unserialize($data);
            return get_class($data);
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getRulesMap()
    {
        $items = [];
        foreach (AuthRule::find()->all() as $rule) {
            $items[AuthRule::getClassName($rule->data)] = $rule->name;
        }
        return $items;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItems()
    {
        return $this->hasMany(AuthItem::class, ['rule_name' => 'name']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $rule = new $this->className;
        $this->data = serialize($rule);

        return parent::beforeSave($insert);
    }
}
