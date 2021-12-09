<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Binding;

use Psr\Http\Message;
use SoapHeader;

interface Binding
{
    /**
     * @param array<mixed, mixed>    $parameters
     * @param array<int, SoapHeader> $soapHeaders

     * @throws Exception\SoapFault
     * @throws Exception\ValueError
     */
    public function request(
        string $operation,
        array $parameters = [],
        array $soapHeaders = []
    ): Message\RequestInterface;

    /**
     * @throws Exception\SoapFault
     * @throws Exception\ValueError
     */
    public function response(string $operation, Message\ResponseInterface $response): Response;
}
