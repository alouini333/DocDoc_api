<?php

namespace App\Exceptions;

use Exception;

class DatesException extends Exception
{
    public function __construct()
    {
        parent::__construct();
        $this->message = 'Please check the start and end dates';
    }
}
