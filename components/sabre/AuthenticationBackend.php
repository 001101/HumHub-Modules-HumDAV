<?php
/**
 * HumHub DAV Access
 *
 * @package humhub.modules.humdav
 * @author KeudellCoding
 */

namespace humhub\modules\humdav\components\sabre;

use humhub\modules\humdav\models\UserToken;
use Sabre\DAV\Auth\Backend\AbstractBasic;
use humhub\modules\user\models\User;
use Sabre\HTTP\Auth\Basic;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;
use Yii;

class AuthenticationBackend extends AbstractBasic {
    function check(RequestInterface $request, ResponseInterface $response) {
        $auth = new Basic(
            $this->realm,
            $request,
            $response
        );

        $userpass = $auth->getCredentials();
        if (!$userpass)
            return [false, "No 'Authorization: Basic' header found. Either the client didn't send one, or the server is misconfigured"];

        $user = User::findOne(['username' => $userpass[0]]);
        if ($user === null)
            return [false, "Username or password was incorrect"];

        if (!$this->validateUserPass($userpass[0], $userpass[1]))
            return [false, "Username or password was incorrect"];

        return [true, $this->principalPrefix . $user->guid];
    }

    protected function validateUserPass($username, $password) {
        $user = User::findOne(['username' => $username]);
        if ($user === null) return false;
        
        foreach (UserToken::findAll(['user_id' => $user->id]) as $userToken) {
            if ($userToken->validateToken($password)) {
                $userToken->updateLastTimeUsed();
                $userToken->save();
                return true;
            }
        }

        $settings = Yii::$app->getModule('humdav')->settings;
        if ($settings->get('enable_password_auth', false)) {
            if ($user->currentPassword !== null && $user->currentPassword->validatePassword($password)) {
                return true;
            }
        }

        return false;
    }
}

