<?php

include 'src/RedisClient.php';

$redis = new RedisClient([
    'hostname' => '127.0.0.1',
    'port' => 6379,
    'database' => 0,
]);
$redis->set('key', 'value');
var_dump($redis->get('key'));