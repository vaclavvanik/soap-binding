<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Binding;

use function in_array;

use const SOAP_1_1;
use const SOAP_1_2;

class Request
{
    /** @var string */
    private $uri;

    /** @var string */
    private $body;

    /** @var string */
    private $soapAction;

    /** @var int */
    private $soapVersion;

    public function __construct(string $uri, string $body, string $soapAction, int $soapVersion)
    {
        if ($uri === '') {
            throw new Exception\ValueError('Uri cannot be empty');
        }

        if ($body === '') {
            throw new Exception\ValueError('Body cannot be empty');
        }

        if ($soapAction === '') {
            throw new Exception\ValueError('Soap action cannot be empty');
        }

        if (in_array($soapVersion, [SOAP_1_1, SOAP_1_2], true) === false) {
            throw new Exception\ValueError('Unsupported SOAP version');
        }

        $this->uri = $uri;
        $this->body = $body;
        $this->soapAction = $soapAction;
        $this->soapVersion = $soapVersion;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getSoapAction(): string
    {
        return $this->soapAction;
    }

    public function getSoapVersion(): int
    {
        return $this->soapVersion;
    }
}
