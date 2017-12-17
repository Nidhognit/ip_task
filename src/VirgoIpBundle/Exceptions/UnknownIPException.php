<?php

namespace VirgoIpBundle\Exceptions;

class UnknownIPException extends \Exception implements APIExceptionInterface
{

    protected $message = 'You are trying to get a non-existent IP address';
}