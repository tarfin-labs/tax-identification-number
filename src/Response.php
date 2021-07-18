<?php

namespace TarfinLabs\VknValidation;

use TarfinLabs\VknValidation\Exceptions\ValidationException;

class Response
{
    private $response;

    public function __construct(string $response)
    {
        $this->response = $this->parse($response);
    }

    public function getStatus(): string
    {
        return $this->response->durum;
    }

    public function getTckn(): string
    {
        return $this->response->tckn;
    }

    public function getStatusText(): string
    {
        return $this->response->durum_text;
    }

    public function getTaxNumber(): string
    {
        return $this->response->vkn;
    }

    public function getTaxOfficeNumber(): string
    {
        return $this->response->vdkodu;
    }

    public function getCompanyTitle(): string
    {
        return $this->response->unvan;
    }

    private function parse(string $response): object
    {
        $result = json_decode($response, false);

        try {
            return $result->data;
        } catch (\Throwable $e) {
            if (isset($result->error)) {
                throw new ValidationException($result->messages[0]->text);
            }

            throw $e;
        }
    }
}