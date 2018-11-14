<?php

use PHPUnit\Framework\TestCase;

/**
 * 有多重依赖的测试
 * Class MultipleDependenciesTest
 */
class MultipleDependenciesTest extends TestCase{

    public function testProducerFirst(){
        $this->assertTrue(true);
        return 'first';
    }

    public function testProducerSecond(){
        $this->assertTrue(true);
        return 'second';
    }

    /**
     * @depends testProducerFirst
     * @depends testProducerSecond
     */
    public function testConsumer(){
        $this->assertEquals(['first', 'second'], func_get_args());
    }
}