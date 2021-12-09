<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Binding;

use SoapHeader;

class Response
{
    /** @var mixed */
    private $result;

    /** @var array<int, SoapHeader> */
    private $headers;

    /**
     * @param mixed                  $result
     * @param array<int, SoapHeader> $headers
     */
    public function __construct($result, array $headers = [])
    {
        $this->result = $result;
        $this->headers = $headers;
    }

    /** @return mixed */
    public function getResult()
    {
        return $this->result;
    }

    /** @return array<int, SoapHeader> */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
