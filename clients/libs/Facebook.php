<?php

namespace filsh\yii2\social\clients\libs;

class Facebook extends \Facebook\Facebook
{
    protected function clearAllPersistentData() {}

    protected function clearPersistentData($key) {}

    protected function getPersistentData($key, $default = false) {}

    protected function setPersistentData($key, $value) {}
}