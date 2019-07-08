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
            [['document_id', 'line_number'], 'default', 'value' => null],
            [['document_id', 'line_number'], 'integer'],
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
