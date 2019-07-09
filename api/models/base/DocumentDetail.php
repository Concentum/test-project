<?php
namespace api\models\base;

class DocumentDetail extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['line_number'], 'default', 'value' => null],
            [['line_number'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
           'line_number' => 'Line Number',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
            'line_number'
        ];
    }
   
}
