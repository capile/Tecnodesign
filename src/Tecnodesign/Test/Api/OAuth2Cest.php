<?php

namespace Tecnodesign\Test\Api;

class OAuth2Cest
{
    protected $configFiles = [], $configs=['oauth2', 'studio'];
    public function _before()
    {
        foreach($this->configs as $fn) {
            if(!file_exists($f=TDZ_ROOT . '/data/config/'.$fn.'.yml') && copy($f.'-example', $f)) {
                $this->configFiles[] = $f;
            }
        }
        if($this->configFiles) {
            touch(TDZ_ROOT.'/app.yml');
        }
        foreach($this->configs as $fn) {
            if(file_exists($f=TDZ_ROOT.'/data/tests/_data/'.$fn.'-before.yml')) {
                exec(TDZ_ROOT.'/app data-import "'.$f.'"');
            }
        }
    }
    // test if it's not authenticated first
    public function accessToken(\ApiTester $I)
    {   // curl -is http://127.0.0.1:9999/examples/oauth2/access_token \
        //   -u test-client:test-secret -d 'grant_type=client_credentials'

        $I->haveHttpHeader('authorization', 'Basic '.base64_encode('test-client:test-secret'));
        $I->sendPost('/examples/oauth2/access_token', ['grant_type'=>'client_credentials']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"access_token":');
        list($accessToken) = $I->grabDataFromResponseByJsonPath('$.access_token');

        $I->deleteHeader('authorization');
        // fetch userinfo
        $I->sendPost('/examples/oauth2/userinfo', ['access_token'=>$accessToken]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['username'=>'test-user']);

        // curl -is http://127.0.0.1:9999/examples/oauth2/userinfo -d access_token=xxx
        $I->sendPost('/examples/oauth2/auth', ['access_token'=>$accessToken]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success'=>true]);

        // fail authentication
        $I->sendPost('/examples/oauth2/auth', ['access_token'=>'blablabla']);
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['error'=>'invalid_token']);

        $I->haveHttpHeader('authorization', 'Bearer '.$accessToken);
        $I->sendGet('/examples/oauth2/auth');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success'=>true]);

        $I->haveHttpHeader('authorization', 'Bearer blablabla');
        $I->sendGet('/examples/oauth2/auth');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['error'=>'invalid_token']);
    }

    public function _after()
    {
        foreach($this->configs as $fn) {
            if(file_exists($f=TDZ_ROOT.'/data/tests/_data/'.$fn.'-after.yml')) {
                exec(TDZ_ROOT.'/app data-import "'.$f.'"');
            }
        }

        if($this->configFiles) {
            foreach($this->configFiles as $i=>$f) {
                unlink($f);
                unset($this->configFiles[$i], $i, $f);
            }
            touch(TDZ_ROOT.'/app.yml');
        }
    }
}
