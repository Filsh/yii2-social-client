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
    
    public function getUserLocation()
    {
        return null;
    }
    
    protected function parseBirthday($birthday)
    {
        // This person's birthday in the format MM/DD/YYYY.
        if($timestamp = strtotime($birthday)) {
            return [date('d', $timestamp), date('m', $timestamp), date('Y', $timestamp)];
        }
        return [];
    }
}