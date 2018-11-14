<?php

use PHPUnit\Framework\TestCase;

/**
 * 用 \depends 标注来表达依赖关系
 * Class StackTest
 */
class StackTest extends TestCase{

    protected $stack;


    protected $service;
    protected $stockInEntity;

    /**
     *  设置基境(fixture)
     */
    protected function setUp(){
        $this->stack = [];

        $this->service = new \ddd\application\stock\StockInService();
        $this->stockInEntity = \ddd\repository\stock\StockInRepository::repository()->findByPk(201804170001);
    }

    public function testEmpty(){
        $this->assertEmpty($this->stack);
        return $this->stockInEntity;
    }

    /**
     * 不使用test前缀命名方法
     * @test
     */
    public function empty(){
        $stack = [];
        $this->assertEmpty($stack);
    }

    /**
     * @depends testEmpty
     */
    public function testPush( $stack){
        var_dump($stack);
        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack) - 1]);
        $this->assertNotEmpty($stack);

        return $stack;
    }

    /**
     * @depends testPush
     */
    public function testPop(array $stack){
        $this->assertEquals('foo', array_pop($stack));
        $this->assertEmpty($stack);
    }

    /**
     * 清理测试所用对象
     */
    protected function tearDown(){
        $this->stack = null;
    }
}
