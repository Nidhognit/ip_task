<?php

namespace VirgoIpBundle\Exceptions;

class UnknownDriverException extends \Exception implements APIExceptionInterface
{

    protected $message = 'Unknown Driver';
}