<?php

namespace filsh\yii2\social\clients;

class Facebook extends BaseClient implements ClientInterface
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