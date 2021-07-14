<?php

namespace TarfinLabs\VknValidation;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class Client
{
    public \GuzzleHttp\Client $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
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
    public function validate(int $vkn, int $cityPlate, string $taxOfficeNumber): string
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
                            'vkn1'              => $vkn,
                            'tckn1'             => '',
                            'iller'             => $cityPlate,
                            'vergidaireleri'    => $taxOfficeNumber,
                        ],
                    ]),
                ],
            ]);

            return $response->getBody()->getContents();
        } catch (RequestException | Exception $e) {
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

            if (!isset($data->token)) {
                throw new Exception('Login failed.');
            }

            return $data->token;
        } catch (RequestException $e) {
            throw $e;
        }
    }
}