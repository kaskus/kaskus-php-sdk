<?php
namespace Kaskus\Exceptions;

class ResourceNotFoundException extends KaskusClientException
{
    public function __construct()
    {
        parent::__construct('Resource not found');
    }
}
