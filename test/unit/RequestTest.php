<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Http;

use PHPUnit\Framework\TestCase;
use VaclavVanik\Soap\Http\Exception\ValueError;
use VaclavVanik\Soap\Http\Request;

use const SOAP_1_1;
use const SOAP_1_2;

final class RequestTest extends TestCase
{
    public function testRequest(): void
    {
        $uri = 'http://localhost';
        $body = '<root/>';
        $soapAction = 'ActionName';
        $soapVersion = SOAP_1_1;

        $soapRequest = new Request($uri, $body, $soapAction, $soapVersion);

        $this->assertSame($uri, $soapRequest->getUri());
        $this->assertSame($body, $soapRequest->getBody());
        $this->assertSame($soapAction, $soapRequest->getSoapAction());
        $this->assertSame($soapVersion, $soapRequest->getSoapVersion());
    }

    /** @return iterable<string, array{string, string, string, int, string}> */
    public function provideInvalidArguments(): iterable
    {
        $uri = 'https://example.com';
        $body = '<root/>';
        $soapAction = 'My';
        $soapVersion = SOAP_1_2;

        yield 'empty_uri' => [
            '',
            $body,
            $soapAction,
            $soapVersion,
            'Uri cannot be empty',
        ];

        yield 'empty_body' => [
            $uri,
            '',
            $soapAction,
            $soapVersion,
            'Body cannot be empty',
        ];

        yield 'empty_soap_action' => [
            $uri,
            $body,
            '',
            $soapVersion,
            'Soap action cannot be empty',
        ];

        yield 'unsupported_soap_version' => [
            $uri,
            $body,
            $soapAction,
            0,
            'Unsupported SOAP version',
        ];
    }

    /** @dataProvider provideInvalidArguments */
    public function testSoapRequestThrowValueErrorException(
        string $uri,
        string $body,
        string $soapAction,
        int $soapVersion,
        string $exceptionMessage
    ): void {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage($exceptionMessage);

        new Request($uri, $body, $soapAction, $soapVersion);
    }
}
