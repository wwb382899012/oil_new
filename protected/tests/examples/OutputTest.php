<?php

use PHPUnit\Framework\TestCase;

/**
 * 对函数或方法的输出进行测试
 * Class OutputTest
 */
class OutputTest extends TestCase{

    public function testExpectFooActualFoo(){
        $this->expectOutputString('foo');
        print 'foo';
    }

    public function testExpectBarActualBaz(){
        $this->expectOutputString('bar');
        print 'baz';
    }
}