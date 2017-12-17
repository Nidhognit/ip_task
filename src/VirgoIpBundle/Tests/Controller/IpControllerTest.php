<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 16.12.17
 * Time: 7:51
 */

namespace VirgoIpBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use VirgoIpBundle\Controller\IpController;

class IpControllerTest extends KernelTestCase
{
    public function setUp()
    {
        $_SERVER['KERNEL_DIR'] = '/var/www/virgoiptask/app/';
        self::bootKernel();
    }

    public function testAddAction()
    {
        $controller = new IpController();
        $controller->setContainer(self::$kernel->getContainer());
        $request = new Request(['ip' => '127.0.0.1']);
        $response = $controller->addAction($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->assertGreaterThanOrEqual(1, $content['count']);
    }

    public function testQueryAction()
    {
        $controller = new IpController();
        $request = new Request(['ip' => '127.0.0.1']);
        $response = $controller->queryAction($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->assertGreaterThanOrEqual(1, $content['count']);
    }
}