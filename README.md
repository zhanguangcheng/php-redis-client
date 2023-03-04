# PHP Redis 客户端简单实现

## 使用示例

```php
<?php

include 'src/RedisClient.php';

$redis = new RedisClient([
    'hostname' => '127.0.0.1',
    'port' => 6379,
    'database' => 0,
]);
$redis->set('key', 'value');
var_dump($redis->get('key'));
```

## 执行测试

```bash
composer install
phpunit
```