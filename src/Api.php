<?php

namespace TarfinLabs\TaxIdentificationNumber;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use TarfinLabs\TaxIdentificationNumber\Exceptions\ApiException;

class Api
{
    public Client $client;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client([
            'base_uri' => 'https://ivd.gib.gov.tr',
            'timeout'  => 10,
        ]);
    }

    /**
     * Validate a VKN.
     *
     * @param int $vkn
     * @param int $cityPlate
     * @param string $taxOfficeNumber
     * @return string
     * @throws GuzzleException
     */
    public function validate(int $vkn, string $taxOfficeNumber): string
    {
        try {
            $token = $this->login();
            $response = $this->client->post('/tvd_server/dispatch', [
                'form_params' => [
                    'cmd'       => 'vergiNoIslemleri_vergiNumarasiSorgulama',
                    'callid'    => 'ff81dd010b12d-8',
                    'pageName'  => 'R_INTVRG_INTVD_VERGINO_DOGRULAMA',
                    'token'     => $token,
                    'jp'        => json_encode([
                        'dogrulama' => [
                            'vkn1'              => strlen($vkn) === 10 ? $vkn : '',
                            'tckn1'             => strlen($vkn) === 11 ? $vkn : '',
                            'vergidaireleri'    => $taxOfficeNumber,
                        ],
                    ]),
                ],
            ]);

            return $response->getBody()->getContents();
        } catch (RequestException | ApiException $e) {
            throw $e;
        }
    }

    /**
     * Login.
     *
     * @return string
     * @throws GuzzleException
     * @throws Exception
     */
    private function login(): string
    {
        try {
            $response = $this->client->post('/tvd_server/assos-login', [
                'form_params' => [
                    'assoscmd'  => 'cfsession',
                    'rtype'     => 'json',
                    'fskey'     => 'intvrg.fix.session',
                    'fuserid'   => 'INTVRG_FIX',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), false);

            if (isset($data->error)) {
                throw new ApiException($data->messages[0]->text);
            }

            return $data->token;
        } catch (RequestException $e) {
            throw $e;
        }
    }
}