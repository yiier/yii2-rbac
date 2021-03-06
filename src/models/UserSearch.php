<?php

namespace yiier\rbac\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends Model
{
    public $id;
    public $username;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'username'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rbac', 'ID'),
            'username' => Yii::t('rbac', 'Username'),
            'name' => Yii::t('rbac', 'Name'),
        ];
    }

    public static function findAll($class)
    {

    }

    /**
     * Create data provider for Assignment model.
     * @param  array $params
     * @param  \yii\db\ActiveRecord $class
     * @param  string $usernameField
     * @param string $idField
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params, $class, $usernameField, $idField)
    {
        $query = $class::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([$idField => $this->id]);
        $query->andFilterWhere(['like', $usernameField, $this->username]);

        return $dataProvider;
    }
}
