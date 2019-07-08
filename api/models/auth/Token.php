<?php
namespace api\models\auth;

use yii;
use yii\db\ActiveRecord;
use api\models\User;
/**
 * This is the model class for table "{{%post}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $expired_at
 * @property string $token
 */
class Token extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%token}}';
    }

    public function generateToken($expire)
    {
        $this->expired_at = $expire;
        $this->token = \Yii::$app->security->generateRandomString();
    }

    public function fields()
    {
        return [
            'token' => 'token',
            'user_id' => 'user_id',
            'username' => function () {
                return $this->user->username;
            },   
            'email' => function () {
                return $this->user->email;
            },   
            'expired' => function () {
                return date(DATE_RFC3339, $this->expired_at);
            },
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

        
} 