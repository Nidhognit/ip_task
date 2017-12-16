<?php

namespace VirgoIpBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use VirgoIpBundle\Exceptions\InvalidIpException;

class IpController extends Controller
{
    /**
     * @Route("/add", name="ip_add", defaults={"_format": "json"})
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidIpException
     */
    public function addAction(Request $request): JsonResponse
    {
        $ip = $this->validateIp($request);

        $count = 1;

        return new JsonResponse(['count' => $count]);
    }

    /**
     * @Route("/query", name="ip_query", defaults={"_format": "json"})
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidIpException
     */
    public function queryAction(Request $request): JsonResponse
    {
        $ip = $this->validateIp($request);

        $count = 1;

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
