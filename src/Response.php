<?php

namespace TarfinLabs\TaxIdentificationNumber;

use TarfinLabs\TaxIdentificationNumber\Exceptions\ValidationException;

class Response
{
    private object $response;

    public function __construct(string $response)
    {
        $this->response = $this->parse($response);
    }

    public function getStatus(): ?string
    {
        return $this->response->durum ?? null;
    }

    public function getTckn(): ?string
    {
        return $this->response->tckn ?? null;
    }

    public function getStatusText(): ?string
    {
        return $this->response->durum_text ?? null;
    }

    public function getTaxNumber(): ?string
    {
        return $this->response->vkn ?? null;
    }

    public function getTaxOfficeNumber(): ?string
    {
        return $this->response->vdkodu ?? null;
    }

    public function getCompanyTitle(): ?string
    {
        return $this->response->unvan ?? null;
    }

    public function isValid(): bool
    {
        return $this->getStatus() === '1';
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