<?php
namespace api\models\auth;

use yii;
use api\models\auth\Token;
use api\models\User;
use yii\base\Model;
/**
 * Signup form
 */
class SignupForm extends Model
{
    
    public $username;
    public $email;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 3, 'max' => 30],

            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            [['username', 'email', 'password'], 'required'],
        ];
    }


    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->save();
        
        $token = new Token();
        $token->user_id = $user->id;
        $token->generateToken(time() + 3600 * 24);

        return $token->save() ? $token : null;
    }

} 