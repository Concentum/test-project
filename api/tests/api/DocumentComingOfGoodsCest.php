<?php
namespace api\tests\api;
use \api\tests\ApiTester;
use api\fixtures\DocumentComingOfGoodsFixture;
use api\fixtures\DocumentComingOfGoodsProductFixture;
use api\fixtures\TokenFixture;
use api\fixtures\UserFixture;
use yii\db\Expression;

class DocumentComingOfGoodsCest
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
            'document-coming-of-goods' => [
                'class' => DocumentComingOfGoodsFixture::className(),
                'dataFile' => codecept_data_dir() . 'document-coming-of-goods.php'
            ], 
            'document-coming-of-goods-product' => [
                'class' => DocumentComingOfGoodsProductFixture::className(),
                'dataFile' => codecept_data_dir() . 'document-coming-of-goods-product.php'
            ],
        ]);
    }

    public function index(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/document-coming-of-goods');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['number' => '00000001'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 4);
    }

    public function indexWithAuthor(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/document-coming-of-goods?expand=author');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            [
                'number' => '00000001',
                'author' => [
                    'username' => 'erau',
                ],
            ]
        ]);
    }

    public function search(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/document-coming-of-goods?filter[number][like]=00000001');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['number' => '00000001'],
        ]);
        $I->dontSeeResponseContainsJson([
            ['number' => '00000002'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 1);
    }

    public function view(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/document-coming-of-goods/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'number' => '00000001',
        ]);
    }

    public function viewWithProductsAndAuthor(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/document-coming-of-goods/1?expand=products,author');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'number' => '00000001',
            'author' => [
                'username' => 'erau',
            ], 
            'products' => [ 
                0 => [
                    'line_number' => 1,
                    'product' => [
                        'description' => 'Ноутбук ASUS VivoBook S15'
                    ]
                ]       
            ],
        ]);
    }

    public function viewNotFound(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/document-coming-of-goods/15');
        $I->seeResponseCodeIs(404);
    }
 
    public function createUnauthorized(ApiTester $I)
    {
        $I->sendPOST('/document-coming-of-goods', [
            'number' => '00000005',
            'warehouse_id' => '1',
            'counterparty_id' => '2',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function create(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPOST('/document-coming-of-goods', [
            'number' => '00000005',
            'warehouse_id' => '1',
            'counterparty_id' => '2',
            'date_time' => '2019-07-12 11:59:48',
            'products' => [
                0 => [
                    'line_number' => 1,
                    'product_id' => 2,
                    'quantity' => 1,
                    'price' => 50000
                ]
            ]
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson([
            'number' => '00000005',
        ]);
    }

    public function updateUnauthorized(ApiTester $I)
    {
        $I->sendPATCH('/document-coming-of-goods/1', [
            'counterparty_id' => 3,
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function update(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPATCH('/document-coming-of-goods/1', [
            'counterparty_id' => 3,
            'products' => [
                0 => [
                    'line_number' => 1,
                    'product_id' => 2,
                    'quantity' => 2,
                    'price' => 50000
                ]
            ]    
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'id' => 1,
            'counterparty' => [
                'id' => 3
            ]
        ]);
    }

    public function updateForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendPATCH('/document-coming-of-goods/1', [
            'counterparty_id' => 3
        ]);
        $I->seeResponseCodeIs(403);
    }

    public function deleteUnauthorized(ApiTester $I)
    {
        $I->sendDELETE('/document-coming-of-goods/1');
        $I->seeResponseCodeIs(401);
    }

    public function delete(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendDELETE('/document-coming-of-goods/1');
        $I->seeResponseCodeIs(204);
    }

    public function deleteForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendDELETE('/document-coming-of-goods/1');
        $I->seeResponseCodeIs(403);
    }
   

}