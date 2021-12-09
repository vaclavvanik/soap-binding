<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Binding\Exception;

use SoapFault as PhpSoapFault;

final class SoapFault extends PhpSoapFault implements Exception
{
    public static function fromSoapFault(PhpSoapFault $e): self
    {
        return new self(
            $e->faultcode,
            $e->faultstring,
            $e->faultactor ?? null,
            $e->detail ?? null,
            $e->faultname ?? null,
        );
    }
}
