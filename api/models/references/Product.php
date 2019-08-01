<?php
namespace api\models\references;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "product".
 *
 *
 */
class Product extends \api\models\base\HierarchicalReference
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'author_id',
                'updatedByAttribute' => false,
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
        ]);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    { 
        return [
            'id',
            'is_deleted',
            'is_folder',
            'parent',
            'code',
            'description',
            'author'
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

}
