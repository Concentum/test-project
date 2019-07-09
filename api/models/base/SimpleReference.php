<?php
namespace api\models\base;

use Yii;
use api\models\User;

/**
 * This is the model abstract class.
 *
 * @property int $id
 * @property bool $is_deleted
 * @property string $version
 * @property string $code
 * @property string $description
 *
 */
class SimpleReference extends Proto
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            'id',
            'is_deleted',
            'code',
            'description',
            'version',
            'author'
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
            [['is_deleted'], 'boolean'],
            [['version'], 'safe'],
            [['code'], 'string', 'max' => 12],
            [['description'], 'string', 'max' => 64],
            [['code', 'description'], 'required'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge([
            'id' => 'ID',
            'is_deleted' => 'Is Deleted',
            'version' => 'Version',
            'code' => 'Code',
            'description' => 'Description',
            'author_id' => 'Author',
        ], parent::attributeLabels());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReference($className)
    {
        return $this->hasOne($className, ['id' => $className.'_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepresentation()
    {
       return $this->description;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

}
