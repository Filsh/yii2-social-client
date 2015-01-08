<?php

namespace filsh\yii2\social\clients;

use Yii;

class Facebook extends \yii\authclient\clients\Facebook implements \filsh\yii2\social\ClientInterface
{
    use ClientTrait;
    
    private $_api;
    
    public function getService()
    {
        if($this->_api === null) {
            $this->_api = new libs\Facebook([
                'app_id' => $this->clientId,
                'app_secret' => $this->clientSecret
            ]);
        }
        
        return $this->_api;
    }
    
    public function setAccessToken($token)
    {
        $params = [];
        if($token instanceof \yii\authclient\OAuthToken) {
            $params = $token->getParams();
        } else if(is_array($token) && isset($token['params'])) {
            $params = $token['params'];
        }
        
        if(!isset($params['access_token'])) {
            throw new \yii\base\Exception('Not supported access token.');
        }
        $this->getService()->setDefaultAccessToken($params['access_token']);
        
        if(!(Yii::$app instanceof \yii\console\Application)) {
            parent::setAccessToken($token);
        }
    }
    
    public function getAccessToken()
    {
        if(!(Yii::$app instanceof \yii\console\Application)) {
            return parent::getAccessToken();
        } else {
            return $this->createToken([
                'params' => [
                    'access_token' => $this->getService()->getDefaultAccessToken(),
                    'token_type' => 'Bearer',
                    'expires_in' => 3600,
                    'created' => time()
                ]
            ]);
        }
    }
    
    public function getUserFullName()
    {
        $attributes = $this->getUserAttributes();
        return $attributes['first_name'] . ' ' . $attributes['last_name'];
    }
    
    public function getUserAvatar()
    {
        /* @var $response \Facebook\FacebookResponse */
        $response = $this->getService()->sendRequest('GET', $this->getUserId() . '/picture', [
            'width' => 500,
            'height' => 500,
            'redirect' => false
        ]);
        
        if(!$response->isError()) {
            $body = $response->getDecodedBody();
            if(!empty($body['data']) && !$body['data']['is_silhouette']) {
                return $body['data']['url'];
            }
        }
        return null;
    }
    
    public function getUserLocation()
    {
        $attributes = $this->getUserAttributes();
        if(isset($attributes['location']) && isset($attributes['location']['id'])) {
            /* @var $response \Facebook\FacebookResponse */
            $response = $this->getService()->sendRequest('GET', $attributes['location']['id']);
            if(!$response->isError()) {
                return $response->getDecodedBody();
            }
        }
        return null;
    }
    
    /**
     * @inheritdoc
     */
    protected function normalizeUserAttributes($attributes)
    {
        if(!empty($attributes['birthday'])) {
            list($attributes['birth_day'], $attributes['birth_month'], $attributes['birth_year']) = $this->parseBirthday($attributes['birthday']);
        }
        return parent::normalizeUserAttributes($attributes);
    }
}