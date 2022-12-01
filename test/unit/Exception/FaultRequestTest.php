<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Binding\Exception;

use PHPUnit\Framework\TestCase;
use SoapFault;
use VaclavVanik\Soap\Binding\Exception\FaultRequest;
use VaclavVanik\Soap\Interpreter;

final class FaultRequestTest extends TestCase
{
    public function testFromRequest(): void
    {
        $fault = Interpreter\Exception\SoapFault::fromSoapFault(new SoapFault('1', ''));
        $operation = 'foo';
        $parameters = [];
        $soapHeaders = [];

        $faultResponse = FaultRequest::fromRequest($fault, $operation, $parameters, $soapHeaders);

        $this->assertSame($fault, $faultResponse->getFault());
        $this->assertSame($operation, $faultResponse->getOperation());
        $this->assertSame($parameters, $faultResponse->getParameters());
        $this->assertSame($soapHeaders, $faultResponse->getSoapHeaders());
    }
}
