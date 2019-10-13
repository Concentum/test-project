<?php
namespace api\models;

use Yii;
use api\models\User;

/**
 *
 * @property int $user_id
 * @property string $item_name
 *
 */
class AuthAssignment extends \yii\db\ActiveRecord
{

    public function fields()
    {  
        return array_merge(parent::fields(), [
            'user_id',
            'item_name',
        ]);
    }

    public function extraFields()
    {
        return array_merge(parent::extraFields(), [
            'user',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    { 
        return array_merge(parent::rules(), [
            [['item_name', 'user_id'], 'required'],
            [['item_name'], 'string', 'max' => 64],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge([
            'item_name' => 'Item name',
            'user_id' => 'User',
        ], parent::attributeLabels());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}
