<?php

namespace VirgoIpBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use VirgoIpBundle\Exceptions\InvalidIpException;
use VirgoIpBundle\Exceptions\UnknownDriverException;
use VirgoIpBundle\Exceptions\UnknownIPException;

class IpController extends Controller
{
    /**
     * @Route("/ip/add", name="ip_add", defaults={"_format": "json"})
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidIpException
     * @throws UnknownDriverException
     */
    public function addAction(Request $request): JsonResponse
    {
        $driverProvider = $this->get('driver.provider');
        $ip = $driverProvider->validateIp($request->get('ip'));
        $count = $driverProvider->addIp($ip);

        return new JsonResponse(['count' => $count]);
    }

    /**
     * @Route("/ip/query", name="ip_query", defaults={"_format": "json"})
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidIpException
     * @throws UnknownIPException
     * @throws UnknownDriverException
     */
    public function queryAction(Request $request): JsonResponse
    {
        $driverProvider = $this->get('driver.provider');
        $ip = $driverProvider->validateIp($request->get('ip'));
        $count = $driverProvider->getIpCount($ip);

        if ($count === 0) {
            throw new UnknownIPException();
        }

        return new JsonResponse(['count' => $count]);
    }
}
