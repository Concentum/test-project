<?php
namespace api\tests\api;
use \api\tests\ApiTester;
use api\fixtures\DocumentMovingOfGoodsFixture;
use api\fixtures\DocumentMovingOfGoodsProductFixture;
use api\fixtures\ProductFixture;
use api\fixtures\WarehouseFixture;
use api\fixtures\TokenFixture;
use api\fixtures\UserFixture;
use yii\db\Expression;

class DocumentMovingOfGoodsCest
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
            'document-moving-of-goods' => [
                'class' => DocumentMovingOfGoodsFixture::className(),
                'dataFile' => codecept_data_dir() . 'document-moving-of-goods.php'
            ], 
            'document-moving-of-goods-product' => [
                'class' => DocumentMovingOfGoodsProductFixture::className(),
                'dataFile' => codecept_data_dir() . 'document-moving-of-goods-product.php'
            ],
            'product' => [
                'class' => ProductFixture::className(),
                'dataFile' => codecept_data_dir() . 'product.php'
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
        $I->sendGET('/document-moving-of-goods');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['number' => '00000001'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 4);
    }

    public function indexWithAuthor(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/document-moving-of-goods?expand=author');
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
        $I->sendGET('/document-moving-of-goods?filter[number][like]=00000001');
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
        $I->sendGET('/document-moving-of-goods/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'number' => '00000001',
        ]);
    }

    public function viewWithProductsAndAuthor(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/document-moving-of-goods/1?expand=products,author');
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
        $I->sendGET('/document-moving-of-goods/15');
        $I->seeResponseCodeIs(404);
    }
 
    public function createUnauthorized(ApiTester $I)
    {
        $I->sendPOST('/document-moving-of-goods', [
            'number' => '00000005',
            'destination_id' => '1',
            'source_id' => '2',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function create(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPOST('/document-moving-of-goods', [
            'number' => '00000005',
            'destination_id' => '1',
            'source_id' => '2',
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
        $I->sendPATCH('/document-moving-of-goods/1', [
            'source_id' => 3,
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function update(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPATCH('/document-moving-of-goods/1', [
            'source_id' => 3,
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
            'source' => [
                'id' => 3
            ]
        ]);
    }

    public function updateForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendPATCH('/document-moving-of-goods/1', [
            'source_id' => 3
        ]);
        $I->seeResponseCodeIs(403);
    }

    public function deleteUnauthorized(ApiTester $I)
    {
        $I->sendDELETE('/document-moving-of-goods/1');
        $I->seeResponseCodeIs(401);
    }

    public function delete(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendDELETE('/document-moving-of-goods/1');
        $I->seeResponseCodeIs(204);
    }

    public function deleteForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendDELETE('/document-moving-of-goods/1');
        $I->seeResponseCodeIs(403);
    }
   

}