<?php
namespace api\tests\api;
use \api\tests\ApiTester;
use api\fixtures\WarehouseFixture;
use api\fixtures\TokenFixture;
use api\fixtures\UserFixture;

class WarehouseCest
{
    public function _before(ApiTester $I)
    {
        $I->haveFixtures([
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
            'token' => [
                'class' => TokenFixture::className(),
                'dataFile' => codecept_data_dir() . 'token.php'
            ],
            'warehouse' => [
                'class' => WarehouseFixture::className(),
                'dataFile' => codecept_data_dir() . 'warehouse.php'
            ],
        ]);
    }

    public function index(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/warehouses');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['description' => 'Склад №1'],
            ['description' => 'Склад №2'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 3);
    }

    public function indexWithAuthor(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/warehouses?expand=author');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            [
                'description' => 'Склад №1',
                'author' => [
                    'username' => 'erau',
                ],
            ]
        ]);
    }

    public function search(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/warehouses?filter[description][like]=Склад №1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['description' => 'Склад №1'],
        ]);
        $I->dontSeeResponseContainsJson([
            ['description' => 'Склад №2'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 1);
    }

    public function view(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/warehouses/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'description' => 'Склад №1',
        ]);
    }

    public function viewNotFound(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/warehouses/15');
        $I->seeResponseCodeIs(404);
    }

    public function createUnauthorized(ApiTester $I)
    {
        $I->sendPOST('/warehouses', [
            'code' => '00000004',
            'description' => 'New Warehouse',
            'author_id' => '1',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function create(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPOST('/warehouses', [
            'code' => '00000004',
            'description' => 'New Warehouse',
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson([
            'code' => '00000004',
            'description' => 'New Warehouse',
        ]);
    }

    public function updateUnauthorized(ApiTester $I)
    {
        $I->sendPATCH('/warehouses/1', [
            'description' => 'New warehouse description',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function update(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPATCH('/warehouses/1', [
            'description' => 'New warehouse description',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'id' => 1,
            'description' => 'New warehouse description',
        ]);
    }

    public function updateForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendPATCH('/warehouses/2', [
            'description' => 'New warehouse description',
        ]);
        $I->seeResponseCodeIs(403);
    }

    public function deleteUnauthorized(ApiTester $I)
    {
        $I->sendDELETE('/warehouses/1');
        $I->seeResponseCodeIs(401);
    }

    public function delete(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendDELETE('/warehouses/1');
        $I->seeResponseCodeIs(204);
    }

    public function deleteForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendDELETE('/warehouses/2');
        $I->seeResponseCodeIs(403);
    }
    
}