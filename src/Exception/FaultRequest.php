<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Binding\Exception;

use RuntimeException;
use SoapHeader;
use VaclavVanik\Soap\Interpreter;

final class FaultRequest extends RuntimeException implements Exception
{
    /** @var Interpreter\Exception\SoapFault */
    private $fault;

    /** @var string */
    private $operation;

    /** @var array<mixed, mixed> */
    private $parameters;

    /** @var array<int, SoapHeader> */
    private $soapHeaders;

    /**
     * @param array<mixed, mixed>    $parameters
     * @param array<int, SoapHeader> $soapHeaders
     */
    private function __construct(
        Interpreter\Exception\SoapFault $fault,
        string $operation,
        array $parameters,
        array $soapHeaders,
        string $message
    ) {
        $this->fault = $fault;
        $this->operation = $operation;
        $this->parameters = $parameters;
        $this->soapHeaders = $soapHeaders;

        parent::__construct($message, 0, $fault);
    }

    /**
     * @param array<mixed, mixed>    $parameters
     * @param array<int, SoapHeader> $soapHeaders
     */
    public static function fromRequest(
        Interpreter\Exception\SoapFault $fault,
        string $operation,
        array $parameters,
        array $soapHeaders
    ): self {
        return new self($fault, $operation, $parameters, $soapHeaders, $fault->getMessage());
    }

    public function getFault(): Interpreter\Exception\SoapFault
    {
        return $this->fault;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    /** @return array<mixed, mixed> */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /** @return array<int, SoapHeader> */
    public function getSoapHeaders(): array
    {
        return $this->soapHeaders;
    }
}
