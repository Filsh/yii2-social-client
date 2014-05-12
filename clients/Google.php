<?php

namespace filsh\yii2\social\clients;

class Google extends \yii\authclient\clients\GoogleOAuth implements \filsh\yii2\social\ClientInterface
{
    use ClientTrait;
    
    private $_client;
    
    private $_oauth2Service;
    
    private $_calendarService;
    
    public function init()
    {
        parent::init();
        
        $this->_client = new \Google_Client();
        $this->_client->setClientId($this->clientId);
        $this->_client->setClientSecret($this->clientSecret);
        $this->_client->setRedirectUri($this->returnUrl);
        $this->_client->setScopes($this->scope);
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
        
        $this->_client->setAccessToken(json_encode($params));
        parent::setAccessToken($token);
    }
    
    public function getUserAvatar(array $params = [])
    {
        $result = $this->getOauth2Service()->userinfo->get();
        if(!empty($result['picture'])) {
            $size = isset($params['size']) ? $params['size'] : 500;
            return $result['picture'] . (!empty($size) ? '?sz=' . $size : '');
        }
        return null;
    }
    
    private function getOauth2Service()
    {
        if($this->_oauth2Service === null) {
            $this->_oauth2Service = new \Google_Service_Oauth2($this->_client);
        }
        return $this->_oauth2Service;
    }
    
    private function getCalendarService()
    {
        if($this->_calendarService === null) {
            $this->_calendarService = new \Google_Service_Calendar($this->_client);
        }
        return $this->_calendarService;
    }
}