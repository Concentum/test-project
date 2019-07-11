<?php
namespace api\tests\api;
use \api\tests\ApiTester;
use common\fixtures\ProductFixture;
use common\fixtures\TokenFixture;
use common\fixtures\UserFixture;

class ProductCest
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
            'product' => [
                'class' => ProductFixture::className(),
                'dataFile' => codecept_data_dir() . 'product.php'
            ],
        ]);
    }

    public function index(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/products');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['description' => 'Ноутбуки'],
            ['description' => 'Ноутбук ASUS VivoBook S15'],
            ['description' => 'Ноутбук Apple MacBook Air'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 3);
    }

    public function indexWithAuthor(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/products?expand=author');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            [
                'description' => 'Ноутбук ASUS VivoBook S15',
                'author' => [
                    'username' => 'erau',
                ],
            ]
        ]);
    }

    public function search(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/products?filter[description][like]=MacBook&filter[is_folder]=0');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['description' => 'Ноутбук Apple MacBook Air'],
        ]);
        $I->dontSeeResponseContainsJson([
            ['description' => 'Ноутбук ASUS VivoBook S15'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 1);
    }

    public function view(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/products/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'description' => 'Ноутбуки',
        ]);
    }

    public function viewNotFound(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/products/15');
        $I->seeResponseCodeIs(404);
    }

    public function createUnauthorized(ApiTester $I)
    {
        $I->sendPOST('/products', [
            'code' => '00000004',
            'description' => 'New Product',
            'author_id' => '1',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function create(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPOST('/products', [
            'code' => '00000004',
            'description' => 'New Product',
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson([
            'author_id' => 1,
            'description' => 'New Product',
        ]);
    }

    public function updateUnauthorized(ApiTester $I)
    {
        $I->sendPATCH('/products/1', [
            'description' => 'New product description',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function update(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPATCH('/products/1', [
            'description' => 'New product description',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'id' => 1,
            'description' => 'New product description',
        ]);
    }

    public function updateForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendPATCH('/products/2', [
            'description' => 'New product description',
        ]);
        $I->seeResponseCodeIs(403);
    }

    public function deleteUnauthorized(ApiTester $I)
    {
        $I->sendDELETE('/products/1');
        $I->seeResponseCodeIs(401);
    }

    public function delete(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendDELETE('/products/1');
        $I->seeResponseCodeIs(204);
    }

    public function deleteForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendDELETE('/products/2');
        $I->seeResponseCodeIs(403);
    }
    
}