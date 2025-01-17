<?php

declare(strict_types=1);

namespace BEAR\Hello\Resource\Page;

use BEAR\Hello\Injector;
use BEAR\Resource\ResourceInterface;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    private ResourceInterface $resource;

    protected function setUp(): void
    {
        $injector = Injector::getInstance('app');
        $this->resource = $injector->getInstance(ResourceInterface::class);
    }

    public function testOnGet(): void
    {
        $ro = $this->resource->get('page://self/index', ['name' => 'BEAR.Sunday']);
        $this->assertSame(200, $ro->code);
        $this->assertSame('Hello BEAR.Sunday', $ro->body['greeting']);
    }
}
