<?php
namespace  api\controllers\base\actions;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;
use yii\rest\Action;
use api\models\ObjectProperty;
use api\models\PropertyValue;

/**
 * UpdateAction implements the API endpoint for updating a model.
 *
 * For more details and usage information on UpdateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UpdateAction extends Action
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;


    /**
     * Updates an existing model.
     * @param string $id the primary key of the model.
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $model->scenario = $this->scenario;

        $params = Yii::$app->getRequest()->getBodyParams();
        $model->load($params, '');
        $errors = [];
        if (!$model->validate()) {
            $errors[] = $model->errors;
        }
        $rows = [];
        
        if (isset($model->details)) {
            foreach($model->details as $key => $detail) {
                if (!isset($params[$key])|| !is_array($params[$key])) continue;
                foreach($params[$key] as $row) {
                    $rowDetail = new $detail();
                    $rowDetail->load($row, '');
                    if (!$rowDetail->validate()) {
                        $errors[$key][] = $rowDetail->errors;
                    }
                    $rows[$key][] = $row;
                }
            }
        }

        if (isset($params['properties']) && is_array($params['properties'])) {
            foreach($params['properties'] as $property) {
                if ($op = ObjectProperty::findOne([
                    'object_class' => $this->modelClass,
                    'property_name' => $property['name']
                ])) {
                    $dm = new yii\base\DynamicModel(['value' => $property['value']]);
                    if (isset($op->rules) && is_array(json_decode($op->rules))) {
                        \Yii::info($op->rules);
                        foreach(json_decode($op->rules) as $rule) {
                            $dm->addRule($rule);
                        }
                        if (!$dm->validate()) {
                            $errors[] = $dm->errors;
                        }
                    }
                    $props[] = [
                        'object_id' => null,
                        'property_id' => $op->id,
                        'value' => $property['value']
                    ];
                }    
            }
        }    

        if (count($errors) > 0) {
            \Yii::info('Model not updated due to validation error.', __METHOD__);
            \Yii::$app->response->setStatusCode(422, 'Data Validation Failed.');
            \Yii::info($errors);
            return $errors;
        } else {
            try {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($model->update(false)) {
                        if (isset($model->details)) {
                            foreach($model->details as $key => $detail) {
                                if (isset($rows[$key])) {
                                    $detail::deleteAll([$detail::$ownerForeignKey => $model->id]);
                                    foreach($rows[$key] as $k => $val) {
                                        $rows[$key][$k][$detail::$ownerForeignKey] = $model->id;
                                    }
                            /*        \Yii::info(array_keys($rows[$key][0]));
                                    \Yii::info(array_values($rows[$key]));  */
                                    \Yii::$app->db->createCommand()->batchInsert($detail::tableName(), array_keys($rows[$key][0]), array_values($rows[$key]))->execute();
                                }    
                            }
                        }
                   
                        if (isset($props) && is_array($props) && count($props) > 0) {
                            foreach($props as $k => $val) {
                                $props[$k]['object_id'] = $model->id;
                        //        \Yii::info($props);
                                \Yii::$app->db->createCommand()->upsert(PropertyValue::tableName(), $props[$k], $props[$k])->execute();
                            }
                            
                        }
                        $transaction->commit();
                    } else {
                        $transaction->rollBack();    
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            } catch (\Exception $e) {
                throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
            }
        }    

        return $model;
    }
}
