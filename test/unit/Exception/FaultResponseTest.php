<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Binding\Exception;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message;
use SoapFault;
use VaclavVanik\Soap\Binding\Exception\FaultResponse;
use VaclavVanik\Soap\Interpreter;

final class FaultResponseTest extends TestCase
{
    public function testFromResponse(): void
    {
        $fault = Interpreter\Exception\SoapFault::fromSoapFault(new SoapFault('1', ''));
        $operation = 'foo';
        $response = $this->createMock(Message\ResponseInterface::class);

        $faultResponse = FaultResponse::fromResponse($fault, $operation, $response);

        $this->assertSame($fault, $faultResponse->getFault());
        $this->assertSame($operation, $faultResponse->getOperation());
        $this->assertSame($response, $faultResponse->getResponse());
    }
}
