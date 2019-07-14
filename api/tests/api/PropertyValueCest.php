<?php
namespace api\tests\api;
use \api\tests\ApiTester;
use api\fixtures\PropertyValueFixture;
use api\fixtures\ObjectPropertyFixture;
use api\fixtures\UserFixture;
use api\fixtures\TokenFixture;

class PropertyValueCest
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
            'property-value' => [
                'class' => PropertyValueFixture::className(),
                'dataFile' => codecept_data_dir() . 'property-value.php'
            ],
            'object-property' => [
                'class' => ObjectPropertyFixture::className(),
                'dataFile' => codecept_data_dir() . 'object-property.php'
            ],
        ]);
    }

    public function index(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/property-values');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['value' => 'USA'],
            ['value' => 'Russia Moscow'],
            ['value' => 'Иванов Иван Иванович'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 3);
    }

    public function search(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/property-values?filter[value][like]=ванов');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['value' => 'Иванов Иван Иванович'],
        ]);
        $I->dontSeeResponseContainsJson([
            ['value' => 'USA'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 1);
    }

    public function view(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/property-values/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'value' => 'USA',
        ]);
    }

    public function viewNotFound(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/property-values/15');
        $I->seeResponseCodeIs(404);
    }

    public function createUnauthorized(ApiTester $I)
    {
        $I->sendPOST('/property-values', [
            'property_id' => 1,
            'object_id' => 2,
            'value' => 'New property value',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function create(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPOST('/property-values', [
            'object_id' => 1,
            'property_id' => 2,
            'value' => 'New property value',
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson([
            'value' => 'New property value',
        ]);
    }

    public function updateUnauthorized(ApiTester $I)
    {
        $I->sendPATCH('/property-values/1', [
            'value' => 'New property value',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function update(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPATCH('/property-values/1', [
            'value' => 'New property value',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'value' => 'New property value',
        ]);
    }

    public function updateForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendPATCH('/property-values/2', [
            'value' => 'New property value',
        ]);
        $I->seeResponseCodeIs(403);
    }

    public function deleteUnauthorized(ApiTester $I)
    {
        $I->sendDELETE('/property-values/1');
        $I->seeResponseCodeIs(401);
    }

    public function delete(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendDELETE('/property-values/1');
        $I->seeResponseCodeIs(204);
    }

    public function deleteForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendDELETE('/property-values/2');
        $I->seeResponseCodeIs(403);
    }
    
} 