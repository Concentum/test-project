<?php
namespace api\tests\api;
use \api\tests\ApiTester;
use api\fixtures\CounterpartyFixture;
use api\fixtures\TokenFixture;
use api\fixtures\UserFixture;

class CounterpartyCest
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
            'counterparty' => [
                'class' => CounterpartyFixture::className(),
                'dataFile' => codecept_data_dir() . 'counterparty.php'
            ],
        ]);
    }

    public function index(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/counterparties');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['description' => 'Юридические лица'],
            ['description' => 'ООО Фабула'],
            ['description' => 'ЗАО Одуванчик'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 3);
    }

    public function indexWithAuthor(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/counterparties?expand=author');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            [
                'description' => 'ООО Фабула',
                'author' => [
                    'username' => 'erau',
                ],
            ]
        ]);
    }

    public function search(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/counterparties?filter[description][like]=Одуванчик');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['description' => 'ЗАО Одуванчик'],
        ]);
        $I->dontSeeResponseContainsJson([
            ['description' => 'ООО Фабула'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 1);
    }

    public function view(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/counterparties/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'description' => 'Юридические лица',
        ]);
    }

    public function viewNotFound(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/counterparties/15');
        $I->seeResponseCodeIs(404);
    }

    public function createUnauthorized(ApiTester $I)
    {
        $I->sendPOST('/counterparties', [
            'code' => '00000004',
            'description' => 'New Counterparty',
            'author_id' => '1',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function create(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPOST('/counterparties', [
            'code' => '00000004',
            'description' => 'New Counterparty',
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson([
            'code' => '00000004',
            'description' => 'New Counterparty',
        ]);
    }

    public function updateUnauthorized(ApiTester $I)
    {
        $I->sendPATCH('/counterparties/1', [
            'description' => 'New counterparty description',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function update(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPATCH('/counterparties/1', [
            'description' => 'New counterparty description',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'id' => 1,
            'description' => 'New counterparty description',
        ]);
    }

    public function updateForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendPATCH('/counterparties/2', [
            'description' => 'New counterparty description',
        ]);
        $I->seeResponseCodeIs(403);
    }

    public function deleteUnauthorized(ApiTester $I)
    {
        $I->sendDELETE('/counterparties/1');
        $I->seeResponseCodeIs(401);
    }
/*
    public function delete(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendDELETE('/counterparties/1');
        $I->seeResponseCodeIs(204);
    }
*/
    public function deleteForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendDELETE('/counterparties/2');
        $I->seeResponseCodeIs(403);
    }
    
}