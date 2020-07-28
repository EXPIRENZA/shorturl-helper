<?php
require './src/Methods.php';

use Expirenza\short\Methods;

class MethodsTest extends PHPUnit\Framework\TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(
            Methods::class,
            new Methods()
        );
    }

    public function testOneMethod(): void
    {
        $this->assertEquals(
            Methods::GET_ONE,
            '/api/short/get/one'
        );
    }

    public function testManyMethod(): void
    {
        $this->assertEquals(
            Methods::GET_MANY,
            '/api/short/get/many'
        );
    }

    public function testUniqueMethod(): void
    {
        $this->assertEquals(
            Methods::GET_UNIQUE,
            '/api/short/get/unique'
        );
    }


}
