<?php

use yii\db\Migration;

/**
 * Class m190708_165617_insert_rbac_data
 */
class m190708_165617_insert_rbac_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $entity = [
            'Product',
            'Counterparty',
            'Warehouse',
            'DocumentComingOfGoods',
            'DocumentExpendOfGoods',
            'DocumentMovingOfGoods'
        ];

        $actions = ['view', 'create', 'update', 'delete'];

        $admin = $auth->createRole('admin');
        $auth->add($admin);

        foreach($entity as $e) {
            foreach($actions as $a) {
                //Для каждой сущности создаём разрешение
                $permission = $auth->createPermission($a.$e);
                $permission->description = $a.' a '.$e;
                $auth->add($permission);
                //Добавляем роли admin это разрешение
                $auth->addChild($admin, $permission);
            }            
        }
        //Добавляем пользователю с id = 1 роль - admin
        $auth->assign($admin, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190708_165617_insert_rbac_data cannot be reverted.\n";
        $this->execute('TRUNCATE TABLE auth_item CASCADE;');
        $this->execute('TRUNCATE TABLE auth_item_child CASCADE;');
        $this->execute('TRUNCATE TABLE auth_assignment CASCADE;');
        $this->execute('TRUNCATE TABLE auth_rule CASCADE;');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190708_165617_insert_rbac_data cannot be reverted.\n";

        return false;
    }
    */
}
