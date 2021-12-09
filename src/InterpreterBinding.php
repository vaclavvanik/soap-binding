<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Binding;

use Psr\Http\Message;
use VaclavVanik\Soap\Interpreter;

final class InterpreterBinding implements Binding
{
    /** @var Interpreter\Interpreter */
    private $interpreter;

    /** @var RequestFactory */
    private $requestFactory;

    public function __construct(Interpreter\Interpreter $interpreter, RequestFactory $requestFactory)
    {
        $this->interpreter = $interpreter;
        $this->requestFactory = $requestFactory;
    }

    /** @inheritdoc */
    public function request(
        string $operation,
        array $parameters = [],
        array $soapHeaders = []
    ): Message\RequestInterface {
        try {
            $interpreterRequest = $this->interpreter->request($operation, $parameters, $soapHeaders);

            $soapRequest = new Request(
                $interpreterRequest->getUri(),
                $interpreterRequest->getBody(),
                $interpreterRequest->getSoapAction(),
                $interpreterRequest->getSoapVersion(),
            );

            return $this->requestFactory->createPsrRequest($soapRequest);
        } catch (Interpreter\Exception\SoapFault $e) {
            throw Exception\SoapFault::fromSoapFault($e);
        } catch (Interpreter\Exception\ValueError $e) {
            throw new Exception\ValueError($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function response(string $operation, Message\ResponseInterface $response): Response
    {
        try {
            $response = $this->interpreter->response($operation, (string) $response->getBody());

            return new Response($response->getResult(), $response->getHeaders());
        } catch (Interpreter\Exception\SoapFault $e) {
            throw Exception\SoapFault::fromSoapFault($e);
        } catch (Interpreter\Exception\ValueError $e) {
            throw new Exception\ValueError($e->getMessage(), $e->getCode(), $e);
        }
    }
}
