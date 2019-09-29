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
        $this->insert('user', [
            'username' => 'erau',
            'auth_key' => 'tUu1qHcde0diwUol3xeI-18MuHkkprQI',
            'password_hash' => '$2y$13$nJ1WDlBaGcbCdbNC5.5l4.sgy.OMEKCqtDQOdQ2OWpgiKRWYyzzne', // password_0
            'password_reset_token' => 'RkD_Jw0_8HEedzLk7MM-ZKEFfYR7VbMr_1392559490',
            'created_at' => '1392559490',
            'updated_at' => '1392559490',
            'email' => 'sfriesen@jenkins.info',
            'status' => '10'
        ]);

        $auth = Yii::$app->authManager;

        $entity = [
            'User',
            'Product',
            'Unit',
            'Counterparty',
            'Contract',
            'Warehouse',
            'ComingOfGoods',
            'ExpendOfGoods',
            'MovingOfGoods',
            'ObjectProperty',
            'PropertyValue'
        ];

        $actions = ['index', 'view', 'create', 'update', 'delete'];

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
