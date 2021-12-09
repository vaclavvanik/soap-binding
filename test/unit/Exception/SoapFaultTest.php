<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Binding\Exception;

use PHPUnit\Framework\TestCase;
use VaclavVanik\Soap\Binding\Exception\SoapFault;

final class SoapFaultTest extends TestCase
{
    public function testFromSoapFaultWithMinimalArguments(): void
    {
        $soapFault = $this->mockSoapFaultWithMinimalArguments();
        $exception = SoapFault::fromSoapFault($soapFault);

        $this->assertSame($soapFault->faultcode, $exception->faultcode);
        $this->assertSame($soapFault->faultstring, $exception->faultstring);
        $this->assertObjectNotHasAttribute('faultactor', $exception);
        $this->assertObjectNotHasAttribute('faultname', $exception);
        $this->assertObjectNotHasAttribute('detail', $exception);
    }

    public function testFromSoapFault(): void
    {
        $soapFault = $this->mockSoapFault();
        $exception = SoapFault::fromSoapFault($soapFault);

        $this->assertSame($soapFault->faultcode, $exception->faultcode);
        $this->assertSame($soapFault->faultstring, $exception->faultstring);
        $this->assertSame($soapFault->faultactor, $exception->faultactor);
        $this->assertSame($soapFault->detail, $exception->detail);
    }

    private function mockSoapFaultWithMinimalArguments(): SoapFault
    {
        return new SoapFault('42', 'my-fault-string');
    }

    private function mockSoapFault(): SoapFault
    {
        return new SoapFault('42', 'my-fault-string', 'my-fault-actor', 'my-detail', 'my-name');
    }
}
