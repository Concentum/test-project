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

        // добавляем разрешение "createProduct"
        $createProduct = $auth->createPermission('createProduct');
        $createProduct->description = 'Create a product';
        $auth->add($createProduct);

        // добавляем разрешение "updateProduct"
        $updateProduct = $auth->createPermission('updateProduct');
        $updateProduct->description = 'Update a product';
        $auth->add($updateProduct);

        // добавляем разрешение "createCounterparty"
        $createCounterparty = $auth->createPermission('createCounterparty');
        $createCounterparty->description = 'create a counterparty';
        $auth->add($createCounterparty);

        // добавляем разрешение "updateCounterparty"
        $updateCounterparty = $auth->createPermission('updateCounterparty');
        $updateCounterparty->description = 'update a counterparty';
        $auth->add($updateCounterparty);

        // добавляем разрешение "createWarehouse"
        $createWarehouse = $auth->createPermission('createWarehouse');
        $createWarehouse->description = 'create a warehouse';
        $auth->add($createWarehouse);

        // добавляем разрешение "updateWarehouse"
        $updateWarehouse = $auth->createPermission('updateWarehouse');
        $updateWarehouse->description = 'update a warehouse';
        $auth->add($updateWarehouse);

        // добавляем разрешение "createComingOfGoods"
        $createComingOfGoods = $auth->createPermission('createComingOfGoods');
        $createComingOfGoods->description = 'create a coming of goods';
        $auth->add($createComingOfGoods);

        // добавляем разрешение "updateComingOfGoods"
        $updateComingOfGoods = $auth->createPermission('updateComingOfGoods');
        $updateComingOfGoods->description = 'update a coming of goods';
        $auth->add($updateComingOfGoods);

        // добавляем разрешение "createExpendOfGoods"
        $createExpendOfGoods = $auth->createPermission('createExpendOfGoods');
        $createExpendOfGoods->description = 'create a expend of goods';
        $auth->add($createExpendOfGoods);

        // добавляем разрешение "updateExpendOfGoods"
        $updateExpendOfGoods = $auth->createPermission('updateExpendOfGoods');
        $updateExpendOfGoods->description = 'update a expend of goods';
        $auth->add($updateExpendOfGoods);

        // добавляем разрешение "createMovingOfGoods"
        $createMovingOfGoods = $auth->createPermission('createMovingOfGoods');
        $createMovingOfGoods->description = 'create a moving of goods';
        $auth->add($createMovingOfGoods);

        // добавляем разрешение "updateMovingOfGoods"
        $updateMovingOfGoods = $auth->createPermission('updateMovingOfGoods');
        $updateMovingOfGoods->description = 'update a moving of goods';
        $auth->add($updateMovingOfGoods);

        // добавляем роль "admin" и даём роли все разрешения"
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $createProduct);
        $auth->addChild($admin, $updateProduct);
        $auth->addChild($admin, $createCounterparty);
        $auth->addChild($admin, $updateCounterparty);
        $auth->addChild($admin, $createWarehouse);
        $auth->addChild($admin, $updateWarehouse);
        $auth->addChild($admin, $createComingOfGoods);
        $auth->addChild($admin, $updateComingOfGoods);
        $auth->addChild($admin, $createExpendOfGoods);
        $auth->addChild($admin, $updateExpendOfGoods);
        $auth->addChild($admin, $createMovingOfGoods);
        $auth->addChild($admin, $updateMovingOfGoods);

        // Назначение ролей пользователям. 1 это ID возвращаемые IdentityInterface::getId()
        // обычно реализуемый в модели User.
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
