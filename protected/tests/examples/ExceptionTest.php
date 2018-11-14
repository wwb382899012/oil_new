<?php

use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase{

    public function testException(){
        $this->expectException(InvalidArgumentException::class);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testException1(){

    }
}