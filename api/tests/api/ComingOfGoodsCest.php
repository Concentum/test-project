<?php
namespace api\tests\api;
use \api\tests\ApiTester;
use api\fixtures\ComingOfGoodsFixture;
use api\fixtures\ComingOfGoodsProductFixture;
use api\fixtures\ProductFixture;
use api\fixtures\CounterpartyFixture;
use api\fixtures\WarehouseFixture;
use api\fixtures\TokenFixture;
use api\fixtures\UserFixture;
use api\fixtures\ObjectPropertyFixture;
use api\fixtures\PropertyValueFixture;
use yii\db\Expression;

class ComingOfGoodsCest
{
    public function _before(ApiTester $I)
    {
        $I->haveFixtures([
            'object-property' => [
                'class' => ObjectPropertyFixture::className(),
                'dataFile' => codecept_data_dir() . 'object-property.php'
            ],
            'property-value' => [
                'class' => PropertyValueFixture::className(),
                'dataFile' => codecept_data_dir() . 'property-value.php'
            ],
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
            'token' => [
                'class' => TokenFixture::className(),
                'dataFile' => codecept_data_dir() . 'token.php'
            ],
            'coming-of-goods' => [
                'class' => ComingOfGoodsFixture::className(),
                'dataFile' => codecept_data_dir() . 'coming-of-goods.php'
            ], 
            'coming-of-goods-product' => [
                'class' => ComingOfGoodsProductFixture::className(),
                'dataFile' => codecept_data_dir() . 'coming-of-goods-product.php'
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
            ]
        ]);
    }

    public function index(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/coming-of-goods');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['number' => '00000001'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 4);
    }

    public function indexWithAuthor(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/coming-of-goods?expand=author');
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
        $I->sendGET('/coming-of-goods?filter[number][like]=00000001');
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
        $I->sendGET('/coming-of-goods/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'number' => '00000001',
        ]);
    }

    public function viewWithProductsAndAuthor(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/coming-of-goods/1?expand=products,author');
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
        $I->sendGET('/coming-of-goods/15');
        $I->seeResponseCodeIs(404);
    }
 
    public function createUnauthorized(ApiTester $I)
    {
        $I->sendPOST('/coming-of-goods', [
            'number' => '00000005',
            'warehouse_id' => '1',
            'counterparty_id' => '2',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function create(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPOST('/coming-of-goods', [
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
                ],
                1 => [
                    'line_number' => 2,
                    'product_id' => 3,
                    'quantity' => 1,
                    'price' => 45000
                ]
            ],
            'properties' => [
                0 => [
                    'name' => 'performer',
                    'value' => 'Кузин Олег Олегович',
                ]    
            ]    
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson([
            'number' => '00000005',
        ]);
    }

    public function createWithBadDetailData(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPOST('/coming-of-goods', [
            'number' => '00000006',
            'warehouse_id' => '15',
            'counterparty_id' => '25',
            'date_time' => '2019-07-12 11:59:48',
            'products' => [
                0 => [
                    'line_number' => 1,
                    'product_id' => 15,
                    'quantity' => 1,
                    'price' => 50000
                ],
                1 => [
                    'line_number' => 1,
                    'product_id' => 16,
                    'quantity' => 1,
                    'price' => 45000
                ]
            ],
            'properties' => [
                0 => [
                    'name' => 'perform',
                    'value' => 'Кузин Олег Олегович',
                ]
            ]
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function updateUnauthorized(ApiTester $I)
    {
        $I->sendPATCH('/coming-of-goods/1', [
            'counterparty_id' => 3,
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function update(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPATCH('/coming-of-goods/1', [
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
        $I->sendPATCH('/coming-of-goods/1', [
            'counterparty_id' => 3
        ]);
        $I->seeResponseCodeIs(403);
    }

    public function deleteUnauthorized(ApiTester $I)
    {
        $I->sendDELETE('/coming-of-goods/1');
        $I->seeResponseCodeIs(401);
    }
/*
    public function delete(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendDELETE('/coming-of-goods/1');
        $I->seeResponseCodeIs(204);
    }
*/
    public function deleteForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-of-user-without-permission');
        $I->sendDELETE('/coming-of-goods/1');
        $I->seeResponseCodeIs(403);
    }
   

}