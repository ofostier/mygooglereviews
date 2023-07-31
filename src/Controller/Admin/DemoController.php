<?php
// modules/your-module/src/Controller/DemoController.php

namespace Mygooglereviews\Controller\Admin;

use Doctrine\Common\Cache\CacheProvider;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
// use Trisula\Mygoog\Entity\MygoogReviews;

class DemoController extends FrameworkBundleAdminController
{
    private $cache;
       
    // you can use symfony DI to inject services
    // public function __construct(CacheProvider $cache)
    // {
    //     $this->cache = $cache;

    //     parent::__construct();
    // }
    
    public function demoAction()
    {
        //$cache = $this->container->get('doctrine.cache');

        //$id_product = (int) Tools::getValue('id_product');

        /** @var EntityManagerInterface $entityManager */
        // $entityManager = $this->container->get('doctrine.orm.entity_manager');
        // $em = $entityManager->getRepository(MygoogReviews::class);

        // $results = $em->findByProductId(1);

        // $serializedComments = [];
        // foreach ($results as $result) {
        //     $serializedComments[] = $result->toArray();
        // }

        return $this->render('@Modules/mygoog/templates/admin/demo.html.twig');
    }
}