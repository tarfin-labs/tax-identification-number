<?php

namespace TarfinLabs\TaxIdentificationNumber\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TarfinLabs\TaxIdentificationNumber\Exceptions\NotFoundException;
use TarfinLabs\TaxIdentificationNumber\Validation;
use TarfinLabs\TaxIdentificationNumber\Exceptions\ValidationException;
use TarfinLabs\TaxIdentificationNumber\Exceptions\ApiException;

class ValidationTest extends TestCase
{
    public function testCanReturnsTaxOfficesByCityPlate()
    {
        $offices = Validation::init()->getTaxOfficesByCityPlate(34);

        $this->assertIsArray($offices);

        foreach ($offices as $office) {
            $this->assertArrayHasKey('code', $office);
            $this->assertArrayHasKey('name', $office);
        }
    }

    public function testCanHandleNotFoundException()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('The city plate is not valid!');

        Validation::init()->getTaxOfficesByCityPlate(234);
    }

    public function testCanValidateVkn(): void
    {
        $loginResponse = '{
            "token": "abcdefghijklmnopqrstuvwxyz",
            "redirectUrl": "main.jsp"
        }';

        $validationResponse = '{
            "data": {
                "durum": "1",
                "tckn": "",
                "durum_text": "FAAL",
                "vkn": "1234567890",
                "vdkodu": "099999",
                "sorgulayantckimlik": "",
                "unvan": "ACME INC."
            },
            "metadata": {
                "optime": "20210714132823"
            }
        }';

        $mockHandler = new MockHandler([
            new Response(200, [], $loginResponse),
            new Response(200, [], $validationResponse),
        ]);

        $validator = Validation::mock(new Client(['handler' => $mockHandler]));
        $response = $validator->validate(1234567890, '099999');

        $this->assertIsObject($response);
        $this->assertEquals("1", $response->getStatus());
        $this->assertEquals("", $response->getTckn());
        $this->assertEquals("FAAL", $response->getStatusText());
        $this->assertEquals("1234567890", $response->getTaxNumber());
        $this->assertEquals("099999", $response->getTaxOfficeNumber());
        $this->assertEquals("ACME INC.", $response->getCompanyTitle());
    }

    public function testCanHandleValidationException(): void
    {
        $loginResponse = '{
            "token": "abcdefghijklmnopqrstuvwxyz",
            "redirectUrl": "main.jsp"
        }';

        $validationResponse = '{
            "metadata": {
                "optime": "20210718201006"
            },
            "messages": [
                {
                    "text": "GİRDİĞİNİZ BİLGİLERDE HATA VARDIR. KONTROL EDEREK TEKRAR DENEYEBİLİRSİNİZ",
                    "type": "1"
                }
            ],
            "error": "1"
        }';

        $mockHandler = new MockHandler([
            new Response(200, [], $loginResponse),
            new Response(200, [], $validationResponse),
        ]);

        $validator = Validation::mock(new Client(['handler' => $mockHandler]));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('GİRDİĞİNİZ BİLGİLERDE HATA VARDIR. KONTROL EDEREK TEKRAR DENEYEBİLİRSİNİZ');

        $validator->validate(1234567890, '099999');
    }

    public function testCanHandleApiException(): void
    {
        $loginResponse = '{
            "error": "1",
            "messages": [
                {
                    "type": "7",
                    "text": "Predefined sessionId or userId does not match for create fixed session"
                }
            ]
        }';

        $mockHandler = new MockHandler([
            new Response(200, [], $loginResponse),
        ]);

        $validator = Validation::mock(new Client(['handler' => $mockHandler]));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Predefined sessionId or userId does not match for create fixed session');

        $validator->validate(1234567890, '099999');
    }
}