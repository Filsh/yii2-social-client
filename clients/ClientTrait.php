<?php

namespace filsh\yii2\social\clients;

trait ClientTrait
{
    public function getUserId()
    {
        $attributes = $this->getUserAttributes();
        if(empty($attributes['id'])) {
            throw new \yii\base\Exception('Not found user id.');
        }
        return $attributes['id'];
    }
}