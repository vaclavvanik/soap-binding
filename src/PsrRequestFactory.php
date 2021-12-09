<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Binding;

use Psr\Http\Message;

use function sprintf;

use const SOAP_1_2;

final class PsrRequestFactory implements RequestFactory
{
    /** @var Message\RequestFactoryInterface */
    private $requestFactory;

    /** @var Message\StreamFactoryInterface */
    private $streamFactory;

    public function __construct(
        Message\RequestFactoryInterface $requestFactory,
        Message\StreamFactoryInterface $streamFactory
    ) {
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    public function createPsrRequest(Request $soapRequest): Message\RequestInterface
    {
        $contentType = sprintf(
            '%s; charset=utf-8',
            $soapRequest->getSoapVersion() === SOAP_1_2 ? 'application/soap+xml' : 'text/xml',
        );

        $request = $this->requestFactory->createRequest('POST', $soapRequest->getUri());
        $request = $request->withBody($this->streamFactory->createStream($soapRequest->getBody()));
        $request = $request->withHeader('Content-Type', $contentType);

        return $request->withHeader('SOAPAction', $soapRequest->getSoapAction());
    }
}
