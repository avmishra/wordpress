<?php

namespace Shoppinglist\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Shoppinglist\ApiBundle\Entity\ShoppinglistItem;

/*
 * 
 */

class ShoppinglistItemController extends BaseController
{
    /**
     * This function will used to add item in shopping list
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Shoppinglist\ApiBundle\Controller\JSonResponse
     * @Rest\Post
     */
    function createAction(Request $request)
    {
        $returnData = array('status' => '404', 'message' => '');

        $apiKey = $request->get('api_key');
        $userData = $this->isValidUser($apiKey);
        if ($userData === NULL) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }
        
        try {
            $shoppinglistItem = new ShoppinglistItem();
            $shoppinglistItem->setFkShoppinglist($request->get('id_shoppinglist'));
            $shoppinglistItem->setFkProduct($request->get('id_product'));
            $shoppinglistItem->setQuantity($request->get('quantity'));
            $shoppinglistItem->setUnit($request->get('unit'));
            $shoppinglistItem->setCreatedAt();
            $shoppinglistItem->setPicked(0);
            
            $validator = $this->get('validator');
            $errorList = $validator->validate($shoppinglistItem);
            if (count($errorList) == 0) {
                $em = $this->getDoctrine()->getManager();
                $savedShoppinglistItem = $em->getRepository('ShoppinglistApiBundle:ShoppinglistItem')->findOneBy(
                    array('fkShoppinglist' => $request->get('id_shoppinglist'), 'fkProduct' => $request->get('id_product'))
                );
                if ($savedShoppinglistItem) {
                    $savedShoppinglistItem->setQuantity($request->get('quantity'))->setUnit($request->get('unit'));
                    $em->flush();
                    $returnData['id_shoppinglist_item'] = $savedShoppinglistItem->getIdShoppinglistItem();
                    $returnData['message'] = 'Item updated successfully';
                } else {
                    $em->persist($shoppinglistItem);
                    $em->flush();
                    if ($shoppinglistItem->getIdShoppinglistItem()) {
                        $returnData['id_shoppinglist_item'] = $shoppinglistItem->getIdShoppinglistItem();
                        $returnData['message'] = 'Item added successfully';
                    }
                }
                $returnData['status'] = '200';
            } else {
                $returnData['message'] = $this->_getErrorMessage($errorList);
            }
            
        } catch (\Exception $exp) {
            $returnData['message'] = $exp->getMessage();
        }

        return $this->getJsonResponse($returnData);
    }
    
    /**
     * This function will used to add item in shopping list
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Shoppinglist\ApiBundle\Controller\JSonResponse
     * @Rest\Get
     */
    function pickedAction(Request $request)
    {
        $returnData = array('status' => '404', 'message' => '');

        $apiKey = $request->get('api_key');
        $userData = $this->isValidUser($apiKey);
        if ($userData === NULL) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }
        try {
            $em = $this->getDoctrine()->getManager();
            $shoppinglistItem = $em->getRepository('ShoppinglistApiBundle:ShoppinglistItem')->findOneBy
                    (array('idShoppinglistItem' => $request->get('id_shoppinglist_item')));
            $shoppinglistItem->setPicked($request->get('status'));
            
            $validator = $this->get('validator');
            $errorList = $validator->validate($shoppinglistItem);
            if (count($errorList) == 0) {
                $em->flush();
                $returnData['id_shoppinglist_item'] = $shoppinglistItem->getIdShoppinglistItem();
                if ($request->get('status')) {
                    $returnData['message'] = 'Item picked successfully';
                } else {
                    $returnData['message'] = 'Item droped off successfully';
                }
                $returnData['status'] = '200';
            } else {
                $returnData['message'] = $this->_getErrorMessage($errorList);
            }
            
        } catch (\Exception $exp) {
            $returnData['message'] = $exp->getMessage();
        }

        return $this->getJsonResponse($returnData);
    }

    
    /**
     * This function will used to add user product
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Shoppinglist\ApiBundle\Controller\JSonResponse
     * @Rest\Get
     */
    function listAction(Request $request)
    {
        $returnData = array('status' => '404', 'message' => '');
        
        $apiKey = $request->get('api_key');
        $userData = $this->isValidUser($apiKey);
        if ($userData === NULL) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }
        
        $shoppinglistItems = $this->getDoctrine()->getManager()->getRepository('ShoppinglistApiBundle:ShoppinglistItem')->getItemsOfShoppinglist($request->get('id_shoppinglist'));

        return $this->getJsonResponse($this->getReturnData($returnData, $shoppinglistItems));
    }
    
    private function _getErrorMessage($errorList)
    {
        $errorMsg = '';

        if (!empty($errorList)) {
            foreach ($errorList as $error) {
                $errorMsg .= $error->getMessage() . '^';
            }
        }

        return $errorMsg;
    }

}
