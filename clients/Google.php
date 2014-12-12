<?php

namespace filsh\yii2\social\clients;

use Yii;
use yii\console\Application;

class Google extends \yii\authclient\clients\GoogleOAuth implements \filsh\yii2\social\ClientInterface
{
    use ClientTrait;
    
    private $_client;
    
    private $_oauth2Service;
    
    private $_plusService;
    
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
    
    public function getService()
    {
        return $this->_client;
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
        
        if(!(Yii::$app instanceof Application)) {
            parent::setAccessToken($token);
        }
    }
    
    public function getUserFullName()
    {
        $attributes = $this->getUserAttributes();
        return $attributes['given_name'] . ' ' . $attributes['family_name'];
    }
    
    public function getUserAvatar()
    {
        $attr = $this->getUserAttributes();
        return !empty($attr['image']) ? $attr['image'] : null;
    }
    
    private function getOauth2Service()
    {
        if($this->_oauth2Service === null) {
            $this->_oauth2Service = new \Google_Service_Oauth2($this->_client);
        }
        return $this->_oauth2Service;
    }
    
    private function getPlusService()
    {
        if($this->_plusService === null) {
            $this->_plusService = new \Google_Service_Plus($this->_client);
        }
        return $this->_plusService;
    }
    
    private function getCalendarService()
    {
        if($this->_calendarService === null) {
            $this->_calendarService = new \Google_Service_Calendar($this->_client);
        }
        return $this->_calendarService;
    }
    
    /**
     * @inheritdoc
     */
    protected function normalizeUserAttributes($attributes)
    {
        $this->normalizeEmail($attributes);
        $this->normalizeName($attributes);
        $this->normalizeBirthday($attributes);
        $this->normalizeImage($attributes);
        
        return parent::normalizeUserAttributes($attributes);
    }
    
    private function normalizeEmail(& $attributes)
    {
        if(!empty($attributes['emails'])) {
            foreach($attributes['emails'] as $email) {
                if($email['type'] === 'account') {
                    $attributes['email'] = $email['value'];
                    break;
                }
            }
            if(empty($attributes['email'])) {
                $attributes['email'] = $attributes['emails'][0]['value'];
            }
        }
    }
    
    private function normalizeName(& $attributes)
    {
        if(!empty($attributes['name'])) {
            $attributes['family_name'] = !empty($attributes['name']['familyName']) ? $attributes['name']['familyName'] : null;
            $attributes['given_name'] = !empty($attributes['name']['givenName']) ? $attributes['name']['givenName'] : null;
        }
        if(empty($attributes['family_name']) && empty($attributes['given_name'])) {
            list($attributes['family_name'], $attributes['given_name']) = explode(' ', $attributes['displayName']);
        }
    }
    
    private function normalizeBirthday(& $attributes)
    {
        $birthday = null;
        if(empty($attributes['birthday'])) {
            $people = $this->getPlusService()->people->get($attributes['id']);
            if(property_exists($people, 'birthday') && !empty($people->birthday)) {
                $birthday = $people->birthday;
            }
        } else {
            $birthday = $attributes['birthday'];
        }
        
        if($birthday !== null) {
            list($attributes['birth_day'], $attributes['birth_month'], $attributes['birth_year']) = $this->parseBirthday($birthday);
        }
    }
    
    private function normalizeImage(& $attributes)
    {
        if(!empty($attributes['image']) && !$attributes['image']['isDefault']) {
            $attributes['picture'] = explode('?sz', $attributes['image']['url'])[0];
        } else {
            $result = $this->getOauth2Service()->userinfo->get();
            if(!empty($result['picture'])) {
                $attributes['picture'] = $result['picture'];
            }
        }
    }
}