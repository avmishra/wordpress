<?php

namespace Shoppinglist\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Shoppinglist\ApiBundle\Entity\Shoppinglist;
use Shoppinglist\ApiBundle\Entity\ShoppinglistItem;
use Shoppinglist\ApiBundle\Entity\User;

/*
 * 
 */

class ShoppinglistController extends BaseController
{

    /**
     * not in use
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Shoppinglist\ApiBundle\Controller\JSonResponse
     * @Rest\Post
     */
    public function createAction(Request $request)
    {
        $returnData = array('status' => '404', 'message' => '');

        $apiKey = $request->get('api_key');
        $userData = $this->isValidUser($apiKey);
        if (!$userData) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }

        try {
            $shoppinglist = new Shoppinglist();
            $shoppinglist->setFkUser($userData);
            $shoppinglist->setShoppinglistName($request->get('shoppinglist_name'));
            $shoppinglist->setCreatedAt();
            $shoppinglist->setStatus(1);
            $validator = $this->get('validator');
            $errorList = $validator->validate($shoppinglist);
            if (count($errorList) == 0) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($shoppinglist);
                $em->flush();
                if ($shoppinglist->getIdShoppinglist()) {
                    $returnData['status'] = '200';
                    $returnData['id_shoppinglist'] = $shoppinglist->getIdShoppinglist();
                    $returnData['message'] = 'New shopping created successfully.';
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
     * not in use
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Shoppinglist\ApiBundle\Controller\JSonResponse
     * @Rest\Post
     */
    public function editAction(Request $request)
    {
        $returnData = array('status' => '404', 'message' => 'No record found.');

        $apiKey = $request->get('api_key');
        $userData = $this->isValidUser($apiKey);
        if (!$userData) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $shoppinglist = $em->getRepository('ShoppinglistApiBundle:Shoppinglist')->findOneBy(
                array('idShoppinglist' => $request->get('id_shoppinglist'), 'fkUser' => $userData->getIdUser())
            );
            
            if ($shoppinglist != NULL) {
                $shoppinglist->setShoppinglistName($request->get('shoppinglist_name'));
                $validator = $this->get('validator');
                $errorList = $validator->validate($shoppinglist);
                if (count($errorList) == 0) {
                    $em->persist($shoppinglist);
                    $em->flush();
                    if ($shoppinglist->getIdShoppinglist()) {
                        $returnData['status'] = '200';
                        $returnData['id_shoppinglist'] = $shoppinglist->getIdShoppinglist();
                        $returnData['message'] = 'Shopping updated successfully';
                    }
                    $returnData['status'] = '200';
                } else {
                    $returnData['message'] = $this->_getErrorMessage($errorList);
                }
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
        if (!$userData) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }
        $shoppinglistArray = array();
        $limit = 30;
        $offset = 0;
        // select all shopping of user
        $shoppinglists = $this->getDoctrine()->getManager()
            ->getRepository('ShoppinglistApiBundle:Shoppinglist')
            ->getAllListing($userData->getIdUser(), $limit, $offset);
        
        foreach ($shoppinglists as $shopping) {
            $shoppinglistArray[$shopping['idShoppinglist']] = array(
                'id_shoppinglist' => $shopping['idShoppinglist'],
                'shoppinglist_name' => $shopping['shoppinglistName'],
                'items' => array(),
                'remaining_item' => 0,
                'sync' => 0,
                'created_at' => $shopping['createdAt']->format('Y-m-d')
            );
        }
        //echo '<pre>';
        //select all items of selected shoppings
        if (!empty($shoppinglistArray)) {
            $shoppinglistItems = $this->getDoctrine()->getManager()
                ->getRepository('ShoppinglistApiBundle:ShoppinglistItem')
                ->getItemsOfShoppinglist(array_keys($shoppinglistArray));
            
            foreach ($shoppinglistItems as $key => $item) {
                $shoppinglistArray[$item['fkShoppinglist']['idShoppinglist']]['items'][] = array(
                    'id_shoppinglist_item' => $item['idShoppinglistItem'],
                    'product_name' => $item['productName'],
                    'unit' => $item['unit'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'],
                    'created_at' => $item['createdAt']->format('Y-m-d'),
                    'picked' => $item['picked'],
                    'sync' => 0
                );
                if ($item['picked'] == 0) {
                    $shoppinglistArray[$item['fkShoppinglist']['idShoppinglist']]['remaining_item']++;
                }
            }
            
        }
        
        return $this->getJsonResponse($this->getReturnData($returnData, $shoppinglistArray));
    }
    
    
    /**
     * Sync the shoppinglist and items
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Shoppinglist\ApiBundle\Controller\JSonResponse
     * @Rest\Post
     */
    public function syncAction(Request $request)
    {
        $returnData = array('status' => '404', 'message' => 'No record found.');
        $apiKey = $request->get('api_key');
        $userData = $this->isValidUser($apiKey);
        if (!$userData) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }
        $syncData = $request->get('data');
        
        try {
            $em = $this->getDoctrine()->getManager();
            
            // first delete all shoppinglist
            foreach ($syncData['deleted_shoppinglist'] as $id) {
                $em->getRepository('ShoppinglistApiBundle:Shoppinglist')->deleteShoppinglistByIds($id, $userData->getIdUser());
            }
            
            // second delete all items
            foreach ($syncData['deleted_items'] as $id) {
               $em->getRepository('ShoppinglistApiBundle:ShoppinglistItem')->deleteShoppinglistItemByIds($id);
            }
            
            // third add/update new shoppinglist
            
            foreach ($syncData['sync_shoppinglist'] as $shoppinglist) {
                // if shopping have id then update otherwise insert
                if (!empty($shoppinglist['id_shoppinglist'])) {
                    $shoppinglistObj = $em->getRepository('ShoppinglistApiBundle:Shoppinglist')->findOneBy(
                        array('idShoppinglist' => $shoppinglist['id_shoppinglist'], 'fkUser' => $userData->getIdUser())
                    );
                    $shoppinglistObj->setShoppinglistName($shoppinglist['shoppinglist_name']);
                    $em->persist($shoppinglistObj);
                } else {
                    $shoppinglistObj = new Shoppinglist();
                    $shoppinglistObj->setFkUser($userData);
                    $shoppinglistObj->setShoppinglistName($shoppinglist['shoppinglist_name']);
                    $shoppinglistObj->setCreatedAt();
                    $shoppinglistObj->setStatus(1);
                    $em->persist($shoppinglistObj);
                    // if shopping having items then save them too
                    foreach ($shoppinglist['items'] as $item) {
                        $shoppinglistItem = new ShoppinglistItem();
                        $shoppinglistItem->setProductName($item['product_name']);
                        $shoppinglistItem->setFkShoppinglist($shoppinglistObj);
                        $shoppinglistItem->setQuantity($item['quantity']);
                        $shoppinglistItem->setUnit($item['unit']);
                        $shoppinglistItem->setCreatedAt();
                        $shoppinglistItem->setPicked($item['picked']);
                        $em->persist($shoppinglistItem);
                    }
                }
                $em->flush();
            }
            
            // fourth add/update item
            
            foreach ($syncData['sync_items'] as $item) {
                
                // if id_shoppinglist_item is given then update the record
                if (!empty($item['id_shoppinglist_item'])) {
                    $shoppinglistItemObj = $em->getRepository('ShoppinglistApiBundle:ShoppinglistItem')->findOneBy(
                        array('idShoppinglistItem' => $item['id_shoppinglist_item'])
                    );
                    if ($shoppinglistItemObj) {
                        $shoppinglistItemObj->setProductName($item['product_name']);
                        $shoppinglistItemObj->setPicked($item['picked']);
                        $shoppinglistItemObj->setQuantity($item['quantity']);
                        $shoppinglistItemObj->setUnit($item['unit']);
                        $em->persist($shoppinglistItemObj);
                        $em->flush();
                    }
                } else {
                    $shoppinglistObj = $em->getRepository('ShoppinglistApiBundle:Shoppinglist')->findOneBy(
                        array('idShoppinglist' => $item['id_shoppinglist'], 'fkUser' => $userData->getIdUser())
                    );
                    if ($shoppinglistObj) {
                        $shoppinglistItemObj = new ShoppinglistItem();
                        $shoppinglistItemObj->setFkShoppinglist($shoppinglistObj);
                        $shoppinglistItemObj->setProductName($item['product_name']);
                        $shoppinglistItemObj->setPicked($item['picked']);
                        $shoppinglistItemObj->setQuantity($item['quantity']);
                        $shoppinglistItemObj->setUnit($item['unit']);
                        $em->persist($shoppinglistItemObj);
                        $em->flush();
                    }
                }
            }
            $returnData['status'] = '200';
            $returnData['message'] = 'Syncing done successfully';
        } catch (\Exception $exp) {
            $returnData['message'] = $exp->getMessage();
        }
        
        return $this->getJsonResponse($returnData);
    }
    
    
   /**
     * not in use
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Shoppinglist\ApiBundle\Controller\JSonResponse
     * @Rest\Post
     */
    public function deleteAction(Request $request)
    {
        $returnData = array('status' => '404', 'message' => 'No record found.');

        $apiKey = $request->get('api_key');
        $userData = $this->isValidUser($apiKey);
        if (!$userData) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $shoppinglist = $em->getRepository('ShoppinglistApiBundle:Shoppinglist')->findOneBy(
                array('idShoppinglist' => $request->get('id_shoppinglist'), 'fkUser' => $userData->getIdUser())
            );
            $em->remove($shoppinglist);
            print_r($em->flush());
        } catch (\Exception $exp) {
            $returnData['message'] = $exp->getMessage();
        }
        
        return $this->getJsonResponse($returnData);
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
