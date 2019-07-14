<?php
namespace api\models\base;

use Yii;
use api\models\User;

/**
 * This is the model abstract class.
 *
 * @property string $version
 *
 */
class InfoRegister extends \yii\db\ActiveRecord
{
    
    public function fields()
    {  
        return array_merge(parent::fields(), [
           'version',
        ]);
    }

    public function extraFields()
    {
        return array_merge(parent::extraFields(), [
            'author' 
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    { 
        return array_merge(parent::rules(), [
            [['version'], 'safe'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge([
            'author_id' => 'Author',
        ], parent::attributeLabels());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

}
