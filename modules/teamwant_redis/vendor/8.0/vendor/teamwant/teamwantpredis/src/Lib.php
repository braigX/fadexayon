<?php

namespace Teamwant\Teamwantpredis;

class Lib {


    public static function prepareData($data) {
        return serialize($data);
    }


    public static function unprepareData($data) {
        return unserialize($data);
    }

}