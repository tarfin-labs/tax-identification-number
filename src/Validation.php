<?php

namespace TarfinLabs\VknValidation;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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
     */
    public function getTaxOfficesByCityPlate(int $cityPlate): array
    {
        $taxOffices = json_decode(file_get_contents("data/taxoffices.json"), true);

        return $taxOffices[sprintf('%03s', $cityPlate)];
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