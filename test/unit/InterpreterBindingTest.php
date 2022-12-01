<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Binding;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SoapHeader;
use Throwable;
use VaclavVanik\Soap\Binding\Exception\FaultRequest;
use VaclavVanik\Soap\Binding\Exception\FaultResponse;
use VaclavVanik\Soap\Binding\Exception\ValueError;
use VaclavVanik\Soap\Binding\InterpreterBinding;
use VaclavVanik\Soap\Binding\Request;
use VaclavVanik\Soap\Binding\RequestFactory;
use VaclavVanik\Soap\Interpreter;

use const SOAP_1_1;

final class InterpreterBindingTest extends TestCase
{
    use ProphecyTrait;

    public function testRequest(): void
    {
        $operation = 'sayHello';
        $parameters = ['name' => 'Venca'];
        $soapHeaders = [];

        /** @var RequestInterface $psrRequest */
        $psrRequest = $this->prophesizePsrRequest()->reveal();

        $interpreterRequest = new Interpreter\Request('uri', 'body', 'action', SOAP_1_1);

        /** @var Interpreter\Interpreter|ObjectProphecy $interpreter */
        $interpreter = $this->prophesizeInterpreter();
        $interpreter->request($operation, $parameters, $soapHeaders)->willReturn($interpreterRequest);
        $interpreter = $interpreter->reveal();

        $requestFactory = new class ($psrRequest) implements RequestFactory {
            /** @var RequestInterface */
            private $psrRequest;

            public function __construct(RequestInterface $psrRequest)
            {
                $this->psrRequest = $psrRequest;
            }

            public function createPsrRequest(Request $soapRequest): RequestInterface
            {
                return $this->psrRequest;
            }
        };

        $binding = new InterpreterBinding($interpreter, $requestFactory);
        $httpBindingRequest = $binding->request($operation, $parameters, $soapHeaders);

        $this->assertSame($psrRequest, $httpBindingRequest);
    }

    public function testResponse(): void
    {
        $operation = 'sayHello';
        $body = 'Hello Venca';

        /** @var RequestFactory|ObjectProphecy $requestFactory */
        $requestFactory = $this->prophesizeRequestFactory()->reveal();

        /** @var ResponseInterface $psrResponse */
        $psrResponse = $this->prophesizePsrResponseGetBody($body)->reveal();

        $interpreterResponse = new Interpreter\Response($body);

        /** @var Interpreter\Interpreter|ObjectProphecy $interpreter */
        $interpreter = $this->prophesizeInterpreter();
        $interpreter->response($operation, $body)->willReturn($interpreterResponse);
        $interpreter = $interpreter->reveal();

        $binding = new InterpreterBinding($interpreter, $requestFactory);
        $response = $binding->response($operation, $psrResponse);

        $this->assertSame($interpreterResponse->getResult(), $response->getResult());
        $this->assertSame($interpreterResponse->getHeaders(), $response->getHeaders());
    }

    /** @return iterable<string, array{InterpreterBinding, string, string}> */
    public function provideRequestException(): iterable
    {
        $operation = 'sayHello';

        /** @var Interpreter\Interpreter|ObjectProphecy $soapFaultInterpreter */
        $soapFaultInterpreter = $this->prophesizeInterpreterRequestWillThrow(
            new Interpreter\Exception\SoapFault(
                '1',
                'a',
            ),
            $operation,
        )->reveal();

        /** @var Interpreter\Interpreter|ObjectProphecy $valueErrorInterpreter */
        $valueErrorInterpreter = $this->prophesizeInterpreterRequestWillThrow(
            new Interpreter\Exception\ValueError(),
            $operation,
        )->reveal();

        /** @var RequestFactory|ObjectProphecy $requestFactory */
        $requestFactory = $this->prophesizeRequestFactory()->reveal();

        yield FaultRequest::class => [
            new InterpreterBinding($soapFaultInterpreter, $requestFactory),
            $operation,
            FaultRequest::class,
        ];

        yield ValueError::class => [
            new InterpreterBinding($valueErrorInterpreter, $requestFactory),
            $operation,
            ValueError::class,
        ];
    }

    /** @dataProvider provideRequestException */
    public function testRequestCatchInterpreterException(
        InterpreterBinding $httpBinding,
        string $operation,
        string $exception
    ): void {
        $this->expectException($exception);

        $httpBinding->request($operation);
    }

    /** @return iterable<string, array{InterpreterBinding, ResponseInterface, string, string}> */
    public function provideResponseException(): iterable
    {
        $operation = 'sayHello';

        $body = '';

        /** @var ResponseInterface $response */
        $response = $this->prophesizePsrResponseGetBody($body)->reveal();

        /** @var Interpreter\Interpreter|ObjectProphecy $soapFaultInterpreter */
        $soapFaultInterpreter = $this->prophesizeInterpreterResponseWillThrow(
            new Interpreter\Exception\SoapFault(
                '1',
                'a',
            ),
            $operation,
            $body,
        )->reveal();

        /** @var Interpreter\Interpreter|ObjectProphecy $valueErrorInterpreter */
        $valueErrorInterpreter = $this->prophesizeInterpreterResponseWillThrow(
            new Interpreter\Exception\ValueError(),
            $operation,
            $body,
        )->reveal();

        /** @var RequestFactory|ObjectProphecy $requestFactory */
        $requestFactory = $this->prophesizeRequestFactory()->reveal();

        yield FaultResponse::class => [
            new InterpreterBinding($soapFaultInterpreter, $requestFactory),
            $operation,
            $response,
            FaultResponse::class,
        ];

        yield ValueError::class => [
            new InterpreterBinding($valueErrorInterpreter, $requestFactory),
            $operation,
            $response,
            ValueError::class,
        ];
    }

    /** @dataProvider provideResponseException */
    public function testResponseCatchInterpreterException(
        InterpreterBinding $httpBinding,
        string $operation,
        ResponseInterface $response,
        string $exception
    ): void {
        $this->expectException($exception);

        $httpBinding->response($operation, $response);
    }

    private function prophesizeInterpreter(): ObjectProphecy
    {
        return $this->prophesize(Interpreter\Interpreter::class);
    }

    /**
     * @param array<mixed, mixed>    $parameters
     * @param array<int, SoapHeader> $soapHeaders
     */
    private function prophesizeInterpreterRequestWillThrow(
        Throwable $e,
        string $operation,
        array $parameters = [],
        array $soapHeaders = []
    ): ObjectProphecy {
        /** @var Interpreter\Interpreter|ObjectProphecy $interpreter */
        $interpreter = $this->prophesizeInterpreter();
        $interpreter->request($operation, $parameters, $soapHeaders)->willThrow($e);

        return $interpreter;
    }

    private function prophesizeInterpreterResponseWillThrow(
        Throwable $e,
        string $operation,
        string $response
    ): ObjectProphecy {
        /** @var Interpreter\Interpreter|ObjectProphecy $interpreter */
        $interpreter = $this->prophesizeInterpreter();
        $interpreter->response($operation, $response)->willThrow($e);

        return $interpreter;
    }

    private function prophesizePsrRequest(): ObjectProphecy
    {
        return $this->prophesize(RequestInterface::class);
    }

    private function prophesizePsrResponse(): ObjectProphecy
    {
        return $this->prophesize(ResponseInterface::class);
    }

    private function prophesizePsrResponseGetBody(string $body): ObjectProphecy
    {
        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesizePsrResponse();
        $response->getBody()->willReturn($body);

        return $response;
    }

    private function prophesizeRequestFactory(): ObjectProphecy
    {
        return $this->prophesize(RequestFactory::class);
    }
}
