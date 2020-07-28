<?php
require './src/Link.php';
require './src/objects/UniqueFormat.php';
require './src/objects/UniqueResponse.php';

use Expirenza\short\Link;
use Expirenza\short\objects\UniqueFormat;
use Expirenza\short\objects\UniqueResponse;


class LinkTest extends PHPUnit\Framework\TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(
            Link::class,
            new Link('test', 'test')
        );
    }

    public function testApiTokenNotSet(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $link = new Link(null, 'test');
    }

    public function testUrlNotSet(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $link = new Link('test', null);
    }

    public function testOneUrl(): void
    {
        $link = new Link('test', 'test', true);
        $this->assertEquals(
            empty($link->getOne('google.com')),
            false
        );
    }

    public function testManyUrl(): void
    {
        $link = new Link('test', 'test', true);
        $urls = ['http://google.com', 'http://facebook.com', 'http://expirenza.com'];
        $result = $link->getMany($urls);

        $this->assertEquals(
            count($urls),
            count($result)
        );
    }

    public function testUniqueUrl(): void
    {
        $link = new Link('test', 'test', true);
        $urls = [
            'http://google.com' => 'random_string_123',
            'http://facebook.com' => 'random_string_321',
            'http://expirenza.com' => 'random_string_456'
        ];

        $source = [];
        foreach ($urls as $url => $code) {
            $object = $link->getUniqueFormatItem($url, $code);
            $this->assertInstanceOf(
                UniqueFormat::class,
                $object
            );
            $source[] = $object;
            unset($object);
        }

        $result = $link->getUniqueMany($source);

        $i = 0;
        foreach ($result as $key => $item) {

            $this->assertInstanceOf(
                UniqueResponse::class,
                $item
            );

            $this->assertEquals(
                $item->getCode(),
                $urls[$item->getUrl()]
            );
            $i++;
        }
    }






}
