<?php

namespace Shoppinglist\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Shoppinglist\ApiBundle\Entity\Shoppinglist;
use Shoppinglist\ApiBundle\Entity\ShoppinglistItem;
use Shoppinglist\ApiBundle\Entity\User;
use Shoppinglist\ApiBundle\Entity\ShoppinglistUser;

class DefaultController extends Controller
{
    /**
     * @Route("/login/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $shoppinglistItem = $em->getRepository('ShoppinglistApiBundle:Shoppinglist')->deleteShoppinglistUserByIds(4,7);
        echo '<pre>';
        print_r($shoppinglistItem);
        \Doctrine\Common\Util\Debug::dump($shoppinglistItem);
        return $this->render('ShoppinglistAdminBundle:Default:index.html.twig', array('name' => ''));
    }
}
