<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Binding;

use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\StreamFactory;
use PHPUnit\Framework\TestCase;
use VaclavVanik\Soap\Binding\PsrRequestFactory;
use VaclavVanik\Soap\Binding\Request;

use const SOAP_1_1;
use const SOAP_1_2;

final class PsrRequestFactoryTest extends TestCase
{
    /** @return iterable<string, array{request: Request, content_type: string}> */
    public function provideSoapRequest(): iterable
    {
        $uri = 'https://example.com';
        $body = '<root/>';
        $soapAction = 'My';

        yield SOAP_1_1 => [
            'request' => new Request($uri, $body, $soapAction, SOAP_1_1),
            'content_type' => 'text/xml; charset=utf-8',
        ];

        yield SOAP_1_2 => [
            'request' => new Request($uri, $body, $soapAction, SOAP_1_2),
            'content_type' => 'application/soap+xml; charset=utf-8',
        ];
    }

    /** @dataProvider provideSoapRequest */
    public function testCreatePsrRequest(Request $soapRequest, string $contentType): void
    {
        $factory = new PsrRequestFactory(new RequestFactory(), new StreamFactory());
        $psrRequest = $factory->createPsrRequest($soapRequest);

        $this->assertSame('POST', $psrRequest->getMethod());
        $this->assertSame($soapRequest->getUri(), (string) $psrRequest->getUri());
        $this->assertSame($soapRequest->getBody(), (string) $psrRequest->getBody());
        $this->assertSame($contentType, $psrRequest->getHeaderLine('Content-Type'));
        $this->assertSame($soapRequest->getSoapAction(), $psrRequest->getHeaderLine('SOAPAction'));
    }
}
