<?php

namespace Acme\HelloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AcmeHelloBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function testEmailAction()
    {
        $message = \Swift_Message::newInstance()
        ->setSubject('Hello Email')
        ->setFrom('avadhesh.project@gmail.com', 'Shoppinglist')
        ->setTo('avadhesh.mishra@gmail.com')
        ->setBody('This is test message.');
        $this->get('mailer')->send($message);
        return $this->render('AcmeHelloBundle:Default:index.html.twig', array('name' => 'test email'));
    }
}
