<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Binding;

use Psr\Http\Message;

interface RequestFactory
{
    public function createPsrRequest(Request $soapRequest): Message\RequestInterface;
}
