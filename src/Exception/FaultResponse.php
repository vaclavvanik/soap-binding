<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Binding\Exception;

use Psr\Http\Message;
use RuntimeException;
use VaclavVanik\Soap\Interpreter;

final class FaultResponse extends RuntimeException implements Exception
{
    /** @var Interpreter\Exception\SoapFault */
    private $fault;

    /** @var string */
    private $operation;

    /** @var Message\ResponseInterface */
    private $response;

    private function __construct(
        Interpreter\Exception\SoapFault $fault,
        string $operation,
        Message\ResponseInterface $response,
        string $message
    ) {
        $this->fault = $fault;
        $this->operation = $operation;
        $this->response = $response;

        parent::__construct($message, 0, $fault);
    }

    public static function fromResponse(
        Interpreter\Exception\SoapFault $fault,
        string $operation,
        Message\ResponseInterface $response
    ): self {
        return new self($fault, $operation, $response, $fault->getMessage());
    }

    public function getFault(): Interpreter\Exception\SoapFault
    {
        return $this->fault;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getResponse(): Message\ResponseInterface
    {
        return $this->response;
    }
}
