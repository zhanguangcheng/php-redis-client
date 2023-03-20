<?php

class RedisClientTest extends PHPUnit\Framework\TestCase
{
    public $hostname = '127.0.0.1';
    public $port = 6379;
    public $username;
    public $password;
    public $database = 10;
    public $connectionTimeout = 1;

    public function __destruct()
    {
        $redis = $this->getRedis();
        try {
            $redis->open();
            $redis->del('key_string');
            $redis->del('string_key1');
            $redis->del('string_key2');
            $redis->del('hash_key');
            $redis->del('list_key');
            $redis->del('pipeline');
        } catch (Exception $e) {
        }
    }

    public function testConstruct()
    {
        $redis = $this->getRedis();
        $this->assertEquals($redis->hostname, $this->hostname);
        $this->assertEquals($redis->port, $this->port);
        $this->assertEquals($redis->username, $this->username);
        $this->assertEquals($redis->password, $this->password);
        $this->assertEquals($redis->database, $this->database);
    }

    public function testOpen()
    {
        $redis = $this->getRedis();
        $redis->open();
        $this->assertNotNull($redis->getSocket());
    }

    public function testOpenError()
    {
        $this->port = 6380;
        $redis = $this->getRedis();
        $this->expectException(Exception::class);
        $redis->open();
    }

    public function testClose()
    {
        $redis = $this->getRedis();
        $redis->open();
        $redis->close();
        $this->assertEquals($redis->getSocket(), null);
    }

    public function testCommandKeys()
    {
        $redis = $this->getRedis();
        $redis->set('key_string', 'value');
        $this->assertEquals(['key_string'], $redis->keys('key_string'));
    }

    public function testCommandStrings()
    {
        $redis = $this->getRedis();
        $this->assertTrue($redis->set('string_key1', '你好 World'));
        $this->assertEquals('你好 World', $redis->get('string_key1'));

        $this->assertTrue($redis->set('string_key2', '1'));
        $this->assertEquals('3', $redis->incrby('string_key2', '2'));
        $this->assertEquals('3', $redis->get('string_key2'));
    }

    public function testCommandHash()
    {
        $redis = $this->getRedis();
        $this->assertEquals('1', $redis->hset('hash_key', 'name', 'Grass'));
        $this->assertEquals('Grass', $redis->hget('hash_key', 'name'));

        $this->assertEquals('1', $redis->hset('hash_key', 'age', '1'));
        $this->assertEquals('3', $redis->hincrby('hash_key', 'age', '2'));
        $this->assertEquals('3', $redis->hget('hash_key', 'age'));
    }

    public function testCommandList()
    {
        $redis = $this->getRedis();
        $this->assertEquals('3', $redis->lpush('list_key', 'Apple', 'Pear', 'Banana'));
        $this->assertEquals('Apple', $redis->rpop('list_key'));
        $this->assertEquals('Banana', $redis->lpop('list_key'));
        $this->assertEquals('1', $redis->llen('list_key'));
    }

    public function testPipeline()
    {
        $redis = $this->getRedis();
        $result = $redis->pipeline()
            ->hset('pipeline', 'name', 'Grass')
            ->hset('pipeline', 'age', 18)
            ->hget('pipeline', 'name')
            ->hget('pipeline', 'age')
            ->exec();
        $this->assertEquals(['1', '1', 'Grass', '18'],$result);
    }

    public function testCommandError()
    {
        $redis = $this->getRedis();
        $this->expectException(Exception::class);
        $redis->executeCommand('NOT_EXISTS_COMMANDS');
    }

    private function getRedis()
    {
        return new RedisClient([
            'hostname' => $this->hostname,
            'port' => $this->port,
            'database' => $this->database,
            'username' => $this->username,
            'password' => $this->password,
            'connectionTimeout' => $this->connectionTimeout,
        ]);
    }
}
