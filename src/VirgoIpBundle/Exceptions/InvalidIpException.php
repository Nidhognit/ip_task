<?php

namespace VirgoIpBundle\Exceptions;

class InvalidIpException extends \Exception implements APIExceptionInterface
{

    protected $message = 'This Ip are invalid';
}