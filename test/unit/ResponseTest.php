<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Binding;

use PHPUnit\Framework\TestCase;
use VaclavVanik\Soap\Binding\Response;

final class ResponseTest extends TestCase
{
    public function testResponse(): void
    {
        $result = 'foo';
        $headers = [];

        $response = new Response($result, $headers);

        $this->assertSame($result, $response->getResult());
        $this->assertSame($headers, $response->getHeaders());
    }
}
