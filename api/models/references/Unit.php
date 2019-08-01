<?php
namespace api\models\references;

/**
 * This is the model class for table "unit".
 *
 *
 */
class Unit extends \api\models\base\SimpleReference
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'unit';
    }

    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['designation'], 'string', 'max' => 16],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {  
        return [
            'id', 'is_deleted', 'code', 'description', 'designation'
        ];
    }

    /**
     * @inheritdoc
     */  
    public function extraFields()
    {
        return array_merge(parent::extraFields(), [
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge([
            'designation' => 'Designation',
        ], parent::attributeLabels());
    }
}