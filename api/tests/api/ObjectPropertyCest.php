<?php
namespace api\tests\api;
use \api\tests\ApiTester;
use api\fixtures\ObjectPropertyFixture;
use api\fixtures\ProductFixture;
use api\fixtures\CounterpartyFixture;
use api\fixtures\WarehouseFixture;
use api\fixtures\UserFixture;
use api\fixtures\TokenFixture;

class ObjectPropertyCest
{
    public function _before(ApiTester $I)
    {
        $I->haveFixtures([
            'object-property' => [
                'class' => ObjectPropertyFixture::className(),
                'dataFile' => codecept_data_dir() . 'object-property.php'
            ],
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
            'token' => [
                'class' => TokenFixture::className(),
                'dataFile' => codecept_data_dir() . 'token.php'
            ],
            'product' => [
                'class' => ProductFixture::className(),
                'dataFile' => codecept_data_dir() . 'product.php'
            ],
            'warehouse' => [
                'class' => WarehouseFixture::className(),
                'dataFile' => codecept_data_dir() . 'warehouse.php'
            ],
            'counterparty' => [
                'class' => CounterpartyFixture::className(),
                'dataFile' => codecept_data_dir() . 'counterparty.php'
            ],
        ]);
    }

    public function index(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/object-properties');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['property_label' => 'Страна происхождения'],
            ['property_label' => 'Юридический адрес'],
            ['property_label' => 'Ответственное лицо'],
            ['property_label' => 'Исполнитель'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 4);
    }

    public function indexWithRules(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/object-properties?expand=rules');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            [
                'property_label' => 'Страна происхождения',
                'rules' => json_encode('')
            ]
        ]);
    }

    public function search(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/object-properties?filter[property_name][like]=origin-country');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['property_label' => 'Страна происхождения'],
        ]);
        $I->dontSeeResponseContainsJson([
            ['property_label' => 'Юридический адрес'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 1);
    }

    public function view(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/object-properties/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'property_label' => 'Страна происхождения',
        ]);
    }

    public function viewNotFound(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/object-properties/15');
        $I->seeResponseCodeIs(404);
    }

    public function createUnauthorized(ApiTester $I)
    {
        $I->sendPOST('/object-properties', [
            'object_class' => 'api\models\Product',
            'property_name' => 'new-product',
            'property_label' => 'New Property',
            'property_rules' => json_encode(''),
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function create(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPOST('/object-properties', [
            'object_class' => 'api\models\Product',
            'property_name' => 'new-product',
            'property_label' => 'New Property',
            'property_rules' =>  json_encode([['value'], 'string', 'max' => 100])
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson([
            'object_class' => 'api\models\Product',
            'property_name' => 'new-product',
            'property_label' => 'New Property',
        ]);
    }

    public function updateUnauthorized(ApiTester $I)
    {
        $I->sendPATCH('/object-properties/1', [
            'object_class' => 'api\models\Product',
            'property_name' => 'new-product',
            'property_label' => 'New Property',
            'property_rules' =>  json_encode([['value'], 'string', 'max' => 100])
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function update(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPATCH('/object-properties/1', [
            'object_class' => 'api\models\Product',
            'property_name' => 'new-product',
            'property_label' => 'New Property label',
            'property_rules' =>  json_encode([['value'], 'string', 'max' => 100])
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'property_label' => 'New Property label',
        ]);
    }

    public function updateForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendPATCH('/object-properties/2', [
            'property_label' => 'New property label',
        ]);
        $I->seeResponseCodeIs(403);
    }

    public function deleteUnauthorized(ApiTester $I)
    {
        $I->sendDELETE('/object-properties/1');
        $I->seeResponseCodeIs(401);
    }

    public function delete(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendDELETE('/object-properties/1');
        $I->seeResponseCodeIs(204);
    }

    public function deleteForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendDELETE('/object-properties/2');
        $I->seeResponseCodeIs(403);
    }
    
} 