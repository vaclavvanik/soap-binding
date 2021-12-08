<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Http;

use Psr\Http\Message;

interface RequestFactory
{
    public function createPsrRequest(Request $soapRequest): Message\RequestInterface;
}
