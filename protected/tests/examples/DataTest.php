<?php

use PHPUnit\Framework\TestCase;

/**
 * 使用返回数组的数组的数据供给器
 * Class DataTest
 */
class DataTest extends TestCase{
    /**
     * @dataProvider additionProvider
     */
    public function testAdd($a, $b, $expected){
        $this->assertEquals($expected, $a + $b);
    }

    public function additionProvider(){
        return [
            [0, 0, 0],
            [0, 1, 1],
            [1, 0, 1],
            [1, 1, 3]
        ];
    }

    /**
     * 使用带有命名数据集的数据供给器
     * @dataProvider additionProvider2
     */
    public function testAdd2($a, $b, $expected){
        $this->assertEquals($expected, $a + $b);
    }

    public function additionProvider2(){
        return [
            'adding zeros'  => [0, 0, 0],
            'zero plus one' => [0, 1, 1],
            'one plus zero' => [1, 0, 1],
            'one plus one'  => [1, 1, 3]
        ];
    }
}