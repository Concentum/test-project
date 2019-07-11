<?php
namespace api\tests\api;
use \api\tests\ApiTester;
use common\fixtures\DocumentComingOfGoodsFixture;
use common\fixtures\DocumentComingOfGoodsProductFixture;
use common\fixtures\TokenFixture;
use common\fixtures\UserFixture;

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

    public function indexWithProducts(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/document-coming-of-goods?expand=products');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            [
                'number' => '00000001',
                'author' => [
                    'username' => 'er-au',
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

    public function viewNotFound(ApiTester $I)
    {   
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/document-coming-of-goods/15');
        $I->seeResponseCodeIs(404);
    }
 
}