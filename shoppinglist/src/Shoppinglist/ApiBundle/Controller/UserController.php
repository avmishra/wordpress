<?php

namespace Shoppinglist\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Controller\Annotations as Rest,
    FOS\RestBundle\Routing\ClassResourceInterface,
    FOS\RestBundle\Util\Codes;
    
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\HttpFoundation\Request;

use JMS\SerializerBundle\Serializer\Serializer;

use Shoppinglist\ApiBundle\Entity\User;

/*
 * 
 */
class UserController extends BaseController
{    
    /**
     * This function will used to check the login
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Shoppinglist\ApiBundle\Controller\JSonResponse
     * @Rest\Post
     */
    public function loginAction(Request $request) 
    {
        $email = $request->get('email');
        $pass = $request->get('password');
        $returnData = array('status' => '404', 'message' => 'Provided email does not exist', 'user_id' => '');
        
        $em = $this->getDoctrine()->getManager();
        try{
            $userData = $em->getRepository('ShoppinglistApiBundle:User')->getUserDetailByEmail($email);
            if ($userData && md5($pass) === $userData['pass']) {
                if ($userData['email_verified']) {
                    $returnData['status'] = '200';
                    $returnData['message'] = '';
                    $returnData['user_id'] = $userData['id'];
                    $returnData['first_name'] = $userData['first_name'];
                    $returnData['last_name'] = $userData['last_name'];
                    $returnData['email'] = $userData['email'];
                    $returnData['api_key'] = $userData['api_key'];
                } else {
                    $returnData['message'] = 'Your email is not verified yet. Please send a test email on "avmishra.org@gmail.com" from your registered email to verify your email.';
                }
            } else {
                $returnData['message'] = 'Email/Password is wrong.';
            }
        } catch (\Exception $exp) {
            $returnData['message'] = $exp->getMessage();
        }
        
        return new JSonResponse(
            $this->container->get('serializer')->serialize($returnData, 'json'),
            200
        );
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Rest\Post
     */
    public function signupAction(Request $request) 
    {
        $returnData = array('status' => '404', 'message' => '', 'data' => array());
        try{
            $emailVerificationCode = bin2hex(openssl_random_pseudo_bytes(3));
            $user = new User();
            $user->setEmail($request->get('email'));
            $user->setPass($request->get('pass'));
            $user->setFirstName($request->get('first_name'));
            $user->setLastName($request->get('last_name'));
            $user->setMobileNo($request->get('mobile_no'));
            $user->setGender($request->get('gender'));
            $user->setApiKey('SL' . time() . 'AV');
            $user->setCreatedAt();
            $user->setUpdatedAt();
            $user->setEmailVerified(0);
            $user->setStatus(1);
            $user->setIsAdmin(0);
            $user->setOauthSource('S');
            $user->setEmailVerificationCode($emailVerificationCode);
            $validator = $this->get('validator');
            $errorList = $validator->validate($user);
            if (count($errorList) == 0) {
                $user->setPass(md5($user->getPass()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                if ($user->getIdUser()) {
                    // send email for email verification
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Email Authentication Code')
                        ->setFrom('avmishra.org@gmail.com', 'Shoppinglist')
                        ->setTo($user->getEmail())
                        ->setBody($this->renderView('ShoppinglistApiBundle:Email:emailVerificationCode.txt.twig', 
                                array(
                                    'code' => $user->getEmailVerificationCode(),
                                    'first_name' => $user->getFirstName()
                                )));
                        $this->get('mailer')->send($message);
                        
                    $returnData['status'] = '200';
                    $returnData['data']['user_id'] = $user->getIdUser();
                    $returnData['data']['email'] = $user->getEmail();
                    $returnData['data']['first_name'] = $user->getFirstName();
                    $returnData['data']['last_name'] = $user->getLastName();
                    $returnData['data']['api_key'] = $user->getApiKey();
                    $returnData['data']['status'] = $user->getStatus();
                    $returnData['data']['email_verified'] = $user->getEmailVerified();
                }
            } else {
                $returnData['message'] = $this->_getErrorMessage($errorList);
            }
        } catch (\Exception $exp) {
            $returnData['message'] = $exp->getMessage();
        }
        return new JSonResponse(
            $this->container->get('serializer')->serialize($returnData, 'json'),
            200
        );
    }
    
    /**
     * This function will make sign up for user when user are comming from 
     * any oauth api
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Rest\Post
     */
    public function oauth_signupAction(Request $request) 
    {
        $returnData = array('status' => '404', 'message' => '', 'data' => array());
        $email = $request->get('email');
        $em = $this->getDoctrine()->getManager();
        $userData = $em->getRepository('ShoppinglistApiBundle:User')->findByEmail($email);
        if ($userData) {
            $userObj = $userData[0];
            $returnData['status'] = '200';
            $returnData['data']['user_id'] = $userObj->getIdUser();
            $returnData['data']['email'] = $userObj->getEmail();
            $returnData['data']['first_name'] = $userObj->getFirstName();
            $returnData['data']['last_name'] = $userObj->getLastName();
            $returnData['data']['api_key'] = $userObj->getApiKey();
            $returnData['data']['status'] = $userObj->getStatus();
            if (!$userObj->getEmailVerified()) {
                $userObj->setEmailVerified(1);
                $em->persist($userObj);
                $em->flush();
            }
            $returnData['data']['email_verified'] = $userObj->getEmailVerified();
        } else {
            try{
                $pass = bin2hex(openssl_random_pseudo_bytes(3));
                $user = new User();
                $gender = 0;
                if ($request->get('gender') == 'male') {
                    $gender = 1;
                }
                $sourceName = 'google';
                $sourceNameDb = 'G';
                if ($request->get('oauth_source') == 'F') {
                    $sourceName = 'facebook';
                    $sourceNameDb = 'F';
                }
                $user->setEmail($request->get('email'));
                $user->setPass($pass);
                $user->setFirstName($request->get('first_name'));
                $user->setLastName($request->get('last_name'));
                $user->setMobileNo('');
                $user->setGender($gender);
                $user->setApiKey('SL' . time() . 'AV');
                $user->setCreatedAt();
                $user->setUpdatedAt();
                $user->setEmailVerified(1);
                $user->setStatus(1);
                $user->setIsAdmin(0);
                $user->setOauthSource($sourceNameDb);
                $validator = $this->get('validator');
                $errorList = $validator->validate($user);
                if (count($errorList) == 0) {
                    $user->setPass(md5($user->getPass()));
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    if ($user->getIdUser()) {
                        // send email for new password
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Signup with ' . $sourceName)
                            ->setFrom('avmishra.org@gmail.com', 'Shoppinglist')
                            ->setTo($user->getEmail())
                            ->setBody($this->renderView('ShoppinglistApiBundle:Email:emailSignupWithFacebook.txt.twig', 
                                    array(
                                        'pass' => $pass,
                                        'email' => $user->getEmail(),
                                        'first_name' => $user->getFirstName()
                                    )));
                            $this->get('mailer')->send($message);

                        $returnData['status'] = '200';
                        $returnData['data']['user_id'] = $user->getIdUser();
                        $returnData['data']['email'] = $user->getEmail();
                        $returnData['data']['first_name'] = $user->getFirstName();
                        $returnData['data']['last_name'] = $user->getLastName();
                        $returnData['data']['api_key'] = $user->getApiKey();
                        $returnData['data']['status'] = $user->getStatus();
                        $returnData['data']['email_verified'] = $user->getEmailVerified();
                    }
                } else {
                    $returnData['message'] = $this->_getErrorMessage($errorList);
                }
            } catch (\Exception $exp) {
                $returnData['message'] = $exp->getMessage();
            }
        }
        
        return new JSonResponse(
            $this->container->get('serializer')->serialize($returnData, 'json'),
            200
        );
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Rest\Post
     */
    public function changepasswordAction(Request $request) 
    {
        $returnData = array('status' => '404', 'message' => '', 'data' => array());
        
        $apiKey = $request->get('api_key');
        $userData = $this->isValidUser($apiKey);
        if (!$userData) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }
        if($userData->getPass() !== md5($request->get('oldpass'))) {
            $returnData['message'] = 'Old password is incorrect.';
            return $this->getJsonResponse($returnData);
        }
        try{
            $validator = $this->get('validator');
            $errorList = $validator->validate($userData);
            if (count($errorList) == 0) {
                $userData->setPass(md5($request->get('newpass')));
                $em = $this->getDoctrine()->getManager();
                $em->persist($userData);
                $em->flush();
                $returnData['status'] = '200';
            } else {
                $returnData['message'] = $this->_getErrorMessage($errorList);
            }
        } catch (\Exception $exp) {
            $returnData['message'] = $exp->getMessage() ;//'Provided email already exist.';
        }
        return new JSonResponse(
            $this->container->get('serializer')->serialize($returnData, 'json'),
            200
        );
    }
    
    /**
     * Email verification
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Rest\Post
     */
    public function verifyAction(Request $request) 
    {
        $returnData = array('status' => '404', 'message' => '', 'data' => array());
        
        $apiKey = $request->get('api_key');
        $userData = $this->isValidUser($apiKey);
        if (!$userData) {
            $returnData['message'] = 'Not authorized';
            return $this->getJsonResponse($returnData);
        }
        if($userData->getEmailVerificationCode() !== $request->get('code')) {
            $returnData['message'] = 'Incorrect code.';
            return $this->getJsonResponse($returnData);
        }
        try{
            $userData->setEmailVerified(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($userData);
            $em->flush();
            $returnData['status'] = '200';
        } catch (\Exception $exp) {
            $returnData['message'] = $exp->getMessage();
        }
        return new JSonResponse(
            $this->container->get('serializer')->serialize($returnData, 'json'),
            200
        );
    }
    
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Rest\Post
     */
    public function sendforgotpasswordcodeAction(Request $request) 
    {
        $returnData = array('status' => '404', 'message' => 'Email id does not exist', 'data' => array());
        $email = $request->get('email');
        $em = $this->getDoctrine()->getManager();
        $userData = $em->getRepository('ShoppinglistApiBundle:User')->findByEmail($email);
        if ($userData) {
            $userObj = $userData[0];
            $passwordVerificationCode = bin2hex(openssl_random_pseudo_bytes(3));
            $userObj->setPasswordVerificationCode($passwordVerificationCode);
            $em->persist($userObj);
            $em->flush();
            // send email for email verification
            $message = \Swift_Message::newInstance()
                ->setSubject('Password Authentication Code')
                ->setFrom('avmishra.org@gmail.com', 'Shoppinglist')
                ->setTo($userObj->getEmail())
                ->setBody($this->renderView('ShoppinglistApiBundle:Email:passwordVerificationCode.txt.twig', 
                        array(
                            'code' => $userObj->getPasswordVerificationCode(),
                            'first_name' => $userObj->getFirstName()
                        )));
                $this->get('mailer')->send($message);
            $returnData['status'] = '200';
            $returnData['message'] = 'An authentication code has been send on your email';
        }
        return new JSonResponse(
            $this->container->get('serializer')->serialize($returnData, 'json'),
            200
        );
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Rest\Post
     */
    public function forgotpasswordAction(Request $request) 
    {
        $returnData = array('status' => '404', 'message' => 'User does not exist', 'data' => array());
        $email = $request->get('email');
        $code = $request->get('code');
        $em = $this->getDoctrine()->getManager();
        $userData = $em->getRepository('ShoppinglistApiBundle:User')->findByEmail($email);
        if ($userData) {
            $userObj = $userData[0];
            if ($userObj->getPasswordVerificationCode() !== $code) {
                $returnData['message'] = 'Invalid Authentication code';
                return new JSonResponse(
                    $this->container->get('serializer')->serialize($returnData, 'json'),
                    200
                );
            }
            $password = bin2hex(openssl_random_pseudo_bytes(3));
            $userObj->setPass(md5($password));
            $em->persist($userObj);
            $em->flush();
            // send email for email verification
            $message = \Swift_Message::newInstance()
                ->setSubject('New Password')
                ->setFrom('avmishra.org@gmail.com', 'Shoppinglist')
                ->setTo($userObj->getEmail())
                ->setBody($this->renderView('ShoppinglistApiBundle:Email:password.txt.twig', 
                        array(
                            'pass' => $password,
                            'first_name' => $userObj->getFirstName()
                        )));
                $this->get('mailer')->send($message);
            $returnData['status'] = '200';
            $returnData['message'] = 'Password has been send on your email';
        }
        return new JSonResponse(
            $this->container->get('serializer')->serialize($returnData, 'json'),
            200
        );
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
