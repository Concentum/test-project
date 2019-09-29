<?php
namespace api\tests\api;
use \api\tests\ApiTester;
use api\fixtures\ContractFixture;
use api\fixtures\TokenFixture;
use api\fixtures\UserFixture;

class ContractCest
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
            'contract' => [
                'class' => ContractFixture::className(),
                'dataFile' => codecept_data_dir() . 'contract.php'
            ],
        ]);
    }

    public function index(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/contracts');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['description' => '№1 ООО Фабула'],
            ['description' => '№2 ЗАО Одуванчик'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 2);
    }

    public function indexWithAuthor(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/contracts?expand=author');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            [
                'description' => '№1 ООО Фабула',
                'author' => [
                    'username' => 'erau',
                ],
            ]
        ]);
    }

    public function search(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/contracts?filter[description][like]=Фабула');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['description' => '№1 ООО Фабула'],
        ]);
        $I->dontSeeResponseContainsJson([
            ['description' => '№2 ЗАО Одуванчик'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 1);
    }

    public function view(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/contracts/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'description' => '№1 ООО Фабула',
        ]);
    }

    public function viewNotFound(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/contracts/15');
        $I->seeResponseCodeIs(404);
    }

    public function createUnauthorized(ApiTester $I)
    {
        $I->sendPOST('/contracts', [
            'code' => '00000004',
            'description' => 'New contract',
            'author_id' => '1',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function create(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPOST('/contracts', [
            'code' => '00000004',
            'number' => '3',
            'date' => '2019-07-12 11:59:48',
            'expires_at' => '2020-07-12 11:59:48',
            'description' => 'New contract',
            'contract_type' => 1,
            'counterparty_id' => 2
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson([
            'code' => '00000004',
            'description' => 'New contract'
        ]);
    }

    public function updateUnauthorized(ApiTester $I)
    {
        $I->sendPATCH('/contracts/1', [
            'description' => 'New contract description',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function update(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPATCH('/contracts/1', [
            'description' => 'New contract description',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'id' => 1,
            'description' => 'New contract description',
        ]);
    }

    public function updateForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendPATCH('/contracts/2', [
            'description' => 'New contract description',
        ]);
        $I->seeResponseCodeIs(403);
    }

    public function deleteUnauthorized(ApiTester $I)
    {
        $I->sendDELETE('/contracts/1');
        $I->seeResponseCodeIs(401);
    }
/*
    public function delete(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendDELETE('/contracts/1');
        $I->seeResponseCodeIs(204);
    }
*/
    public function deleteForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendDELETE('/contracts/2');
        $I->seeResponseCodeIs(403);
    }
    
}