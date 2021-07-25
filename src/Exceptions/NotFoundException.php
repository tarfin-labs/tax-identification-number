<?php

namespace TarfinLabs\VknValidation\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}