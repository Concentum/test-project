<?php
namespace api\tests\api;
use \api\tests\ApiTester;
use api\fixtures\TokenFixture;
use api\fixtures\UserFixture;
use api\models\User;
class ProfileCest
{ /*
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
        ]);
    }
    public function access(ApiTester $I)
    {
        $I->sendGET('/profile');
        $I->seeResponseCodeIs(401);
    }
    public function authenticated(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendGET('/profile');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'id' => 1,
            'username' => 'erau',
            'email' => 'sfriesen@jenkins.info',
        ]);
        $I->dontSeeResponseJsonMatchesJsonPath('$.password_hash');
    }
    public function expired(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-expired');
        $I->sendGET('/profile');
        $I->seeResponseCodeIs(401);
    }
    public function update(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPATCH('/profile', [
            'description' => $description = 'New Description',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'description' => $description,
        ]);
    }
    */
} 