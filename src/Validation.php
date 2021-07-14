<?php

namespace TarfinLabs\VknValidation;

use GuzzleHttp\Exception\GuzzleException;

class Validation
{
    private static $instance;

    private Client $client;

    protected function __construct() {
        $this->client = new Client();
    }

    protected function __clone() {}

    protected function __wakeup()
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

    /**
     * Returns tax offices in given city.
     *
     * @param int $cityPlate
     * @return array
     */
    public function getTaxOfficeByCityPlate(int $cityPlate): array
    {
        $taxOffices = json_decode(file_get_contents("taxoffices.json"), true);

        return $taxOffices[sprintf('%03s', $cityPlate)];
    }

    /**
     * Validate a vkn.
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
            return $this->client->validate($vkn, $cityPlate, $taxOfficeNumber);
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}