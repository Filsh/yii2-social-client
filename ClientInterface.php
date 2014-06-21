<?php

namespace filsh\yii2\social;

interface ClientInterface extends \yii\authclient\ClientInterface
{
    public function getService();
    public function getUserId();
    public function getUserAvatar();
    public function getUserLocation();
}