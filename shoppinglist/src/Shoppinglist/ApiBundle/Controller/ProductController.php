<?php

namespace Shoppinglist\ApiBundle\Controller;

use Shoppinglist\ApiBundle\Entity\Product;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/*
 * 
 */
class ProductController extends BaseController
{    
    /**
     * This function will used to add user product
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Shoppinglist\ApiBundle\Controller\JSonResponse
     * @Rest\Post
     */
    public function createAction(Request $request) 
    {
        $returnData = array('status' => '404', 'message' => '', 'product_id' => '');
        
        $apiKey = $request->get('api_key');
        $userData = $this->isValidUser($apiKey);
        if ($userData === NULL) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }
        
        try{
            $product = new Product();
            $product->setAddedBy($userData['id_user']);
            $product->setProductName($request->get('product_name'));
            $product->setFkCategory($request->get('fk_category'));
            $product->setCreatedAt();
            $product->setUpdatedAt();
            $product->setStatus(1);
            $validator = $this->get('validator');
            $errorList = $validator->validate($product);
            if (count($errorList) == 0) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();
                if ($product->getIdProduct()) {
                    $returnData['status'] = '200';
                    $returnData['data']['product_id'] = $product->getIdProduct();
                    $returnData['message'] = 'Product added successfully';
                }
                $returnData['status'] = '200';
            } else {
                $returnData['message'] = $this->_getErrorMessage($errorList);
            }
        } catch (\Exception $exp) {
            $returnData['message'] = $exp->getMessage() ;
        }
        return $this->getJsonResponse($returnData);
    }
    
    /**
     * This function will used list all product
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Shoppinglist\ApiBundle\Controller\JSonResponse
     * @Rest\Get
     */
    public function listAction(Request $request)
    {
        $returnData = array('status' => '404', 'message' => '');
        
        $apiKey = $request->get('api_key');
        $userData = $this->isValidUser($apiKey);
        if ($userData === NULL) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }
        $categoryId = ($request->get('category_id')) ? $request->get('category_id') : NULL;
        $allProducts = $this->getDoctrine()->getManager()->getRepository('ShoppinglistApiBundle:Product')->getAllProducts($categoryId);
        
        return $this->getJsonResponse($this->getReturnData($returnData, $allProducts));
    }
    
    /**
     * This function will used list all product
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Shoppinglist\ApiBundle\Controller\JSonResponse
     * @Rest\Get
     */
    public function categoryListAction(Request $request)
    {
        $returnData = array('status' => '404', 'message' => '');
        
        $apiKey = $request->get('api_key');
        $userData = $this->isValidUser($apiKey);
        if ($userData === NULL) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }
        
        $allCategories = $this->getDoctrine()->getManager()->getRepository('ShoppinglistApiBundle:Category')->getAllCategories();
        
        return $this->getJsonResponse($this->getReturnData($returnData, $allCategories));
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
