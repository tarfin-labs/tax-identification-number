<?php

namespace TarfinLabs\TaxIdentificationNumber;

use GuzzleHttp\Client;
use TarfinLabs\TaxIdentificationNumber\Exceptions\NotFoundException;
use Throwable;

class Validation
{
    private static $instance;

    private Api $api;

    protected function __construct(?Client $client = null) {
        $this->api = new Api($client);
    }

    protected function __clone() {}

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    public static function init(): ?Validation
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public static function mock(Client $client)
    {
        return new static($client);
    }

    /**
     * Returns tax offices in given city.
     *
     * @param int $cityPlate
     * @return array
     * @throws NotFoundException
     */
    public function getTaxOfficesByCityPlate(int $cityPlate): array
    {
        $taxOffices = json_decode(file_get_contents( __DIR__ . "/../data/taxoffices.json"), true);

        if (!$offices = $taxOffices[sprintf('%03s', $cityPlate)] ?? null) {
            throw new NotFoundException('The city plate is not valid!');
        }

        return array_map(function ($office) {
            return [
                'code' => $office['kod'],
                'name' => $office['vdadi'],
            ];
        }, $offices);
    }

    /**
     * Validate a vkn.
     *
     * @param int $vkn
     * @param string $taxOfficeNumber
     * @return object
     * @throws Throwable
     */
    public function validate(int $vkn, string $taxOfficeNumber): object
    {
        try {
            $result = $this->api->validate($vkn, $taxOfficeNumber);
            return new Response($result);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}