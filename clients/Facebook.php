<?php

namespace filsh\yii2\social\clients;

class Facebook extends \yii\authclient\clients\Facebook implements \filsh\yii2\social\ClientInterface
{
    private $_api;
    
    public function init()
    {
        parent::init();
        $this->_api = new libs\Facebook(array(
            'appId' => $this->clientId,
            'secret' => $this->clientSecret
        ));
    }
}