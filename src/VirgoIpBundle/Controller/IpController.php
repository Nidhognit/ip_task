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
     * @Route("/add", name="ip_add", defaults={"_format": "json"})
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidIpException
     * @throws UnknownDriverException
     */
    public function addAction(Request $request): JsonResponse
    {
        $ip = $this->validateIp($request);
        $driverProvider = $this->get('driver.provider');
        $count = $driverProvider->addIp($ip);

        return new JsonResponse(['count' => $count]);
    }

    /**
     * @Route("/query", name="ip_query", defaults={"_format": "json"})
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidIpException
     * @throws UnknownIPException
     * @throws UnknownDriverException
     */
    public function queryAction(Request $request): JsonResponse
    {
        $ip = $this->validateIp($request);
        $driverProvider = $this->get('driver.provider');
        $count = $driverProvider->getIpCount($ip);

        if ($count === 0) {
            throw new UnknownIPException();
        }

        return new JsonResponse(['count' => $count]);
    }

    /**
     * @param Request $request
     * @return string
     * @throws InvalidIpException
     */
    protected function validateIp(Request $request): string
    {
        $ip = $request->get('ip');
        if ($ip === null || !filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidIpException();
        }

        return $ip;
    }
}
