# Soap Binding

This package provides binding SOAP messages to PSR-7 HTTP messages.
The main purpose of this library is to use it together with [PSR-18 HTTP Client](https://www.php-fig.org/psr/psr-18/).

## Install

You can install this package via composer. 

``` bash
composer require vaclavvanik/soap-binding
```

This package needs [PSR-17 HTTP Factories](https://www.php-fig.org/psr/psr-17/) implementation.
You can use e.g. [Laminas Diactoros](https://github.com/laminas/laminas-diactoros).

``` bash
composer require vaclavvanik/soap-binding laminas/laminas-diactoros
```

## Usage

[Binding::request()](src/Binding.php) embeds SOAP request messages into PSR-7 HTTP requests.

```php
<?php

declare(strict_types=1);

use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\Request\Serializer;
use Laminas\Diactoros\StreamFactory;
use VaclavVanik\Soap\Binding\InterpreterBinding;
use VaclavVanik\Soap\Binding\PsrRequestFactory;
use VaclavVanik\Soap\Interpreter\PhpInterpreter;

$factory = new PsrRequestFactory(new RequestFactory(), new StreamFactory());
$interpreter = PhpInterpreter::fromWsdl('http://www.dneonline.com/calculator.asmx?wsdl');

$httpBinding = new InterpreterBinding($interpreter, $factory);
$psrRequest = $httpBinding->request('Add', ['Add' => ['intA' => 1, 'intB' => 3]]);

echo Serializer::toString($psrRequest);
```

Output:

```
POST /calculator.asmx HTTP/1.1
SOAPAction: http://tempuri.org/Add
Content-Type: text/xml; charset="utf-8"
Host: www.dneonline.com

<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://tempuri.org/">
    <SOAP-ENV:Body>
        <ns1:Add>
            <ns1:intA>1</ns1:intA>
            <ns1:intB>3</ns1:intB>
        </ns1:Add>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

[Binding::response()](src/Binding.php) embeds PSR-7 HTTP response into SOAP response object.

Send `$psrRequest` created above with any PSR HTTP Client and get SOAP response.

```php
<?php
//$psrResponse = $client->sendRequest($psrRequest);

$result = $httpBinding->response('Add', $psrResponse);

print_r($result->getResult());
```

Output:

```php
stdClass Object
(
    [AddResult] => 4
)
```

## Exceptions

- [Exception\SoapFault](src/Exception/SoapFault.php) if soap fault thrown.
- [Exception\ValueError](src/Exception/ValueError.php) if required argument is incorrect.

## Run check - coding standards and php-unit

Install dependencies:

```bash
make install
```

Run check:

```bash
make check
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
