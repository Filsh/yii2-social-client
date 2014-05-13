<?php

namespace filsh\yii2\social\clients;

class Facebook extends \yii\authclient\clients\Facebook implements \filsh\yii2\social\ClientInterface
{
    use ClientTrait;
    
    private $_api;
    
    public function init()
    {
        parent::init();
        $this->_api = new libs\Facebook([
            'appId' => $this->clientId,
            'secret' => $this->clientSecret
        ]);
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
        $this->_api->setAccessToken($params['access_token']);
        parent::setAccessToken($token);
    }
    
    public function getUserAvatar()
    {
        $result = $this->_api->api($this->getUserId() . '/picture', 'GET', [
            'width' => 500,
            'height' => 500,
            'redirect' => false
        ]);

        if(!empty($result['data']) && !$result['data']['is_silhouette']) {
            return $result['data']['url'];
        }
        return null;
    }
    
    public function getUserLocation()
    {
        $attributes = $this->getUserAttributes();
        if(isset($attributes['location']) && isset($attributes['location']['id'])) {
            return $this->_api->api($attributes['location']['id'], 'GET');
        }
        return null;
    }
    
    /**
     * @inheritdoc
     */
    protected function normalizeUserAttributes($attributes)
    {
        if(!empty($attributes['birthday'])) {
            // This person's birthday in the format MM/DD/YYYY.
            $birthday = strtotime($attributes['birthday']);
            if($birthday) {
                $attributes['birth_day'] = date('d', $birthday);
                $attributes['birth_month'] = date('m', $birthday);
                $attributes['birth_year'] = date('Y', $birthday);
            }
        }
        return parent::normalizeUserAttributes($attributes);
    }
}