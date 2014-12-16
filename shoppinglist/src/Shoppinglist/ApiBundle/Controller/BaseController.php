<?php

namespace Shoppinglist\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Routing\ClassResourceInterface,
    FOS\RestBundle\Util\Codes;
    
use Symfony\Component\HttpFoundation\JsonResponse;

use JMS\SerializerBundle\Serializer\Serializer;

class BaseController extends FOSRestController implements ClassResourceInterface
{

    public function isValidUser($apiKey)
    {
        $em = $this->getDoctrine()->getManager();
        //return $em->getRepository('ShoppinglistApiBundle:User')->getUserByApiKey($apiKey);
        $userData = $em->getRepository('ShoppinglistApiBundle:User')->findByApiKey($apiKey);
        if (!empty($userData)) {
            return $userData[0];
        }
        return false;
    }

    public function getJsonResponse($returnData)
    {
        return new JSonResponse(
            $this->container->get('serializer')->serialize($returnData, 'json'),
            200
        );
    }
    
    public function getReturnData(&$returnData, $data)
    {
        $returnData['status'] = '200';
        $returnData['data'] = $data;
        
        return $returnData;
    }
}
