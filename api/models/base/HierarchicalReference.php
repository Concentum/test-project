<?php
namespace api\models\base;

/**
 * This is the model abstract class.
 *
 * @property bool $is_folder
 * @property int $parent_id
 *
 */
class HierarchicalReference extends SimpleReference
{
  
    public function fields()
    { 
        return array_merge(parent::fields(), [
            'is_folder' => 'is_folder',
            'parent_id' => 'parent_id',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {  
        return array_merge(parent::rules(), [
            [['is_folder'], 'boolean'],
            [['parent_id'], 'default', 'value' => null],
            [['parent_id'], 'integer'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => $this::className(), 'targetAttribute' => ['parent_id' => 'id'], 'filter' => ['is_folder' => true]],
        ]);
    } 

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge([
    //        'is_folder' => 'Is Folder',
            'parent_id' => 'Parent',
        ], parent::attributeLabels());
    }

    public function getParent()
    {
        return $this->hasOne($this->class, ['id' => 'parent_id']);
    }
    
    public function getChildren()
    {
        return $this->hasMany($this->class, ['parent_id' => 'id']);
    }

    public function isFolder()
    {
        return isset($this->is_folder) && $this->is_folder;
    }    
}
