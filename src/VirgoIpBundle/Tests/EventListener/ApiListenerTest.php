<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 16.12.17
 * Time: 7:09
 */

namespace VirgoIpBundle\Tests\EventListener;

use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest;
use VirgoIpBundle\EventListener\ApiListener;
use VirgoIpBundle\Exceptions\InvalidIpException;

class ApiListenerTest extends KernelTestCase
{

    /**
     * @param string $exceptionClass
     * @param string $message
     * @dataProvider getExceptionsProvider
     */
    public function testApiException(string $exceptionClass, string $message)
    {
        $apiListener = new ApiListener();
        $exception = new $exceptionClass();

        $kernel = new KernelForTest('test', true);
        $exceptionEvent = new GetResponseForExceptionEvent($kernel, new Request(), 1, $exception);
        $apiListener->onKernelException($exceptionEvent);
        $response = $exceptionEvent->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->assertEquals($message, $content['errorMessage']);

    }

    public function getExceptionsProvider()
    {
        return [
            'API exception' => [InvalidIpException::class, 'This Ip are invalid'],
            'Custom exception' => [DBALException::class, 'Unknown error'],
        ];
    }

}