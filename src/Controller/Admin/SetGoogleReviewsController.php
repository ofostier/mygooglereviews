<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
//declare(strict_types=1);

namespace Mygooglereviews\Controller\Admin;
//namespace Module\DemoDoctrine\Controller\Admin;

use Configuration;
use Context;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

// use Symfony\Component\Form\AbstractType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
// use Symfony\Component\Form\Extension\Core\Type\NumberType;
// use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// use Symfony\Component\Form\Extension\Core\Type\TextType;
// use Symfony\Component\Form\FormBuilderInterface;

//use Mygooglereviews\Form\CommentType;
//use PrestaShop\Module\MyGoogleReviews\Controller\Admin\Form\CommentType;
use Mygooglereviews\Entity\Mygooglereviews;
use Mygooglereviews\Entity\Mygooglereviewsreviews;
use Mygooglereviews\Entity\Mygooglereviewsscore;
use Mygooglereviews\Entity\Mygooglereviewsscored;
//use Mygooglereviews\Form\CommentType;
use Mygooglereviews\Form\EstablishmentType;
use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\HelperForm;
use PrestaShopBundle\Entity\Shop;
use Tools;
use Media;

use function Clue\StreamFilter\append;

class SetGoogleReviewsController extends FrameworkBundleAdminController
{
    //const TAB_CLASS_NAME = 'AdminDemoControllerTabsManualTab';
    const TAB_CLASS_NAME = 'AdminMygooglereviewsSetGoogleReviews';
    private $MYGGOGLEREVIEWS_GOOGLE_TOKEN;
    private $MYGGOGLEREVIEWS_GOOGLE_PLACEID;
    private $MYGGOGLEREVIEWS_ADDRESS;
    private $entityManager;
    private $reviews;

    public function __construct()
    {
        $this->MYGGOGLEREVIEWS_GOOGLE_TOKEN = Configuration::get('MYGGOGLEREVIEWS_GOOGLE_TOKEN');
        $this->MYGGOGLEREVIEWS_GOOGLE_PLACEID = Configuration::get('MYGGOGLEREVIEWS_GOOGLE_PLACEID');
        $this->MYGGOGLEREVIEWS_ADDRESS = Configuration::get('MYGGOGLEREVIEWS_ADDRESS');

        if (empty($this->MYGGOGLEREVIEWS_GOOGLE_TOKEN) || empty($this->MYGGOGLEREVIEWS_ADDRESS) || empty($this->MYGGOGLEREVIEWS_GOOGLE_PLACEID)) {

            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminModules').'&configure=mygooglereviews');
        }

        // $link_get_placeid = $this->getAdminLink('ps_controller_ajax_get', array('route' => 'ps_controller_ajax_get'));
        // $link_refresh_reviews = Context::getContext()->link->getAdminLink('ps_controller_ajax_getreviews', true, array('route' => 'ps_controller_ajax_getreviews'));
        //$this->getAdminLink('ps_controller_ajax_getreviews');//, array('route' => 'ps_controller_ajax_get', 'action' => 'index'));
        //echo $link_refresh_reviews;
        //Add const def to JS (global page)
    //     Media::addJsDef(['adminlink_get_placeid' => $link_get_placeid, 'adminlink_refresh_reviews' => $link_refresh_reviews]);
    //     echo ">>>>>>" . $link_get_placeid;
    //     echo ">>>>>>" . $link_refresh_reviews;
    }

    public function getEM(EntityManagerInterface $entityManager)
    {
        // 3. Update the value of the private entityManager variable through injection
        $this->entityManager = $entityManager;

        //parent::__construct();
        return $entityManager;
    }

    /**
     * @AdminSecurity("is_granted('read', 'AdminMyGoogleReviewsManualTab')")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        //return $this->render('@Modules/mygooglereviews/views/templates/admin/manual_tab.html.twig');
        //return $this->render('@Modules/mygoog/templates/admin/demo.html.twig');

        $data = "";
        $form = $this->createForm(EstablishmentType::class);
        //$form = $this->createForm($this->buildForm());

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        
        if($form->isSubmitted() && $form->isValid()) {
            

            $mygooglereviews = $em->getRepository(Mygooglereviews::class)->findOneBy(array('placeid' => $form->get('placeid')->getData() )); //new Mygooglereviews();

            //var_dump($form->get('address')->getData());
           // var_dump($form->get('placeid')->getData());
            if (!$mygooglereviews) {
                $mygooglereviews = new mygooglereviews(); 
            }
            

            $mygooglereviews->setAddress($form->get('address')->getData());
            $mygooglereviews->setPlaceid($form->get('placeid')->getData());

            $em->persist($mygooglereviews);
            $em->flush();

            $this->addFlash("success","Form has been correctly submited.   ");
            //$this->addFlash("error","500 === Form has been correctly submited.   ");
            
            //dump($form->getData());
        }
        else {
            // ????
            // echo "TRUNCATE TABLE";
            // $connection = $this->getDoctrine()->getConnection();
            // $platform   = $connection->getDatabasePlatform();
            // $connection->executeUpdate($platform->getTruncateTableSQL('ps_mygooglereviews', true /* whether to cascade */));
        }

        //$em = $this->getDoctrine()->getManager();
        //$address = $em->getRepository(Mygooglereviews::class)->findAll($criteria="id=1");
        $address = $em->getRepository(Mygooglereviews::class)->findOneBy(array('id' => 1 ));
        //$placeid = $address->getPlaceid();
        if ($address == null) {
            $placeid = "";
            $addressId = "";
            echo "EMPTY DATA ADDRESS";
        }
        else {

            //$placeid = (array($address->getPLaceid()));
            $placeid = $address->getPLaceid();
            $addressId = $address->getId();
            echo "FOUNDED DATA ADDRESS";
        }

        $scores = $em->getRepository(Mygooglereviewsscore::class)->findBy(array('establishment_id' => $placeid));
        //var_dump($scores);
        $reviews = $em->getRepository(Mygooglereviewsreviews::class)->findBy(array('placeid' => $placeid));
        //var_dump($reviews);
        //$data = $em->getRepository(Mygooglereviews::class)->find(1);
        $data = (object) array('id' => $addressId, 'address' => $this->MYGGOGLEREVIEWS_ADDRESS, 'placeid' => $placeid );

        $form = $this->createForm(EstablishmentType::class, $data);

        $shop = $this->getContext()->shop->id; //$this->getAdminLink();
        $base_url = $this->getContext()->link->getBaseLink();
        //var_dump($data);
        //exit();

        $value = Tools::getValue('MYGGOGLEREVIEWS_ADDRESS');
        $value2 = Configuration::get('MYGGOGLEREVIEWS_ADDRESS');
        //var_dump($value);

        return $this->render(
            // "@Modules/mybasicmodule/views/templates/admin/comment.html.twig",
            //"@Modules/mygooglereviews/views/templates/admin/set.html.twig",
            "@Modules/mygooglereviews/views/templates/admin/index.html.twig",
            [
                'placeid' => $this->MYGGOGLEREVIEWS_GOOGLE_PLACEID,
                'address' => $this->MYGGOGLEREVIEWS_ADDRESS,
                'data' => $address,
                'scores' => $scores,
                'reviews' => $reviews,
                "form" => $form->createView(),
                "info" => $value2,
                //"form2" => $this->esForm(),
            ]
        );

        //return $this->render('@Modules/mygooglereviews/views/templates/admin/set.html.twig');
        //return $this->render('@Modules/mygooglereviews/views/templates/admin/index.html.twig');
    }

    public function initFirstRow(){

    }

    public function esForm() 
    {
        $helper = new HelperForm();

        $form = [
            [
                'form' => [
                    'legend' => [       
                        'title' => 'Edit carrier',       
                        'icon' => 'icon-cogs'   
                    ],   
                    'input' => [       
                        [           
                            'type' => 'text',
                            'name' => 'shipping_method',
                        ],
                    ],
                    'submit' => [
                        'title' => 'Save',       
                        'class' => 'btn btn-default pull-right'   
                    ],
                ],
            ],
        ];
        
        return $helper->generateForm($form);
    }
    
    public function ajaxgetplaceidAction() {
        //var_dump("AJAX GET ACTION");

        //return new Response(json_encode($_POST));
        
        $API_KEY = $this->MYGGOGLEREVIEWS_GOOGLE_TOKEN; //'AIzaSyAm6X8wBA-nm_RJ1Xg3qgUEiUx124hg41o';
    
        $places = array();
    
        $request = "https://maps.googleapis.com/maps/api/place/textsearch/json?";
        $params  = array(
            "query" => $_POST['establishment_address'],
            "key"   => $_POST['token_api'],
            "language" => "fr²"
        );
    
        $request .= http_build_query($params);
    
        $json = file_get_contents($request);

        return new Response(json_encode($json));

    }

    public function ajaxgetreviewsAction($query="") {
        
        $API_KEY = $this->MYGGOGLEREVIEWS_GOOGLE_TOKEN;
    
        $reviews = array();
    
        $request = "https://maps.googleapis.com/maps/api/place/details/json?";
        $params  = array(
            "placeid" => $_POST['placeid'],
            "key"   => $API_KEY,
            "language" => "fr"
        );
    
        $request .= http_build_query($params);
    
        $json = file_get_contents($request);
        //echo json_encode($json);
        //exit();
        $data = json_decode($json, true);
        //$data = json_decode($json);
        
        $result = json_decode(json_encode ( $json ) , true);

        $sql = $this->sqlInsertScore($_POST['placeid'], $data['result']['rating'], $data['result']['user_ratings_total']);
        // return new Response(json_encode(
        //     [
        //         "result" => ["sql"=>$sql]
        //     ]
        // ));

        $this->reviews =  $data['result']['reviews'];
        $sql2 = $this->sqlInsertReviews($_POST['placeid'], $data['result']['reviews']); //$_POST['placeid'], $data['result']['reviews']);

        return new Response(json_encode(
            [
                "result" => ["sql2"=> $this->reviews, "cnt"=> $sql2]
                
            ]
        ));

        return new Response($json);
        return new Response(json_encode($json));
    }

    public function sqlInsertScore($placeid, $score=0, $nbvotes=0) {

        $em = $this->container->get('doctrine.orm.entity_manager');
        $mygooglereviewsscore = $em->getRepository(Mygooglereviewsscore::class)->findOneBy(array('establishment_id' => $placeid ));
        if (!$mygooglereviewsscore) {
            $mygooglereviewsscore = new mygooglereviewsscore(); 
        }
        
        $mygooglereviewsscore->setEstablishment_id($placeid);
        $mygooglereviewsscore->setEstablishment_score($score);
        $mygooglereviewsscore->setEstablishment_nbvote($nbvotes);

        $em->persist($mygooglereviewsscore);
        $em->flush();

        return $mygooglereviewsscore->getId();

    }

    //public function sqlInsertReviews($placeid=0, $reviews=[]) {
    public function sqlInsertReviews($placeid = "placeiiiiid", $reviews) {


        //$emd = $this->container->get('doctrine.orm.entity_manager');
        $em = $this->container->get('doctrine.orm.entity_manager');

        $query = $em->createQuery(
            'DELETE FROM Mygooglereviews\Entity\Mygooglereviewsreviews e WHERE e.placeid = :placeid'
         )->setParameter('placeid', $placeid)->execute();
  
        // sleep(2);
        $cnt=[];

        foreach ($reviews as $review) {
            
            array_push($cnt, $review['author_name']);

            
            $mygooglereviewsreviews = new Mygooglereviewsreviews(); 

            $mygooglereviewsreviews->setPlaceid($placeid);
            $mygooglereviewsreviews->setAuthor_name($review['author_name']);
            $mygooglereviewsreviews->setAuthor_url($review['author_url']);
            $mygooglereviewsreviews->setLanguage($review['language']);
            $mygooglereviewsreviews->setOriginal_language($review['original_language']);
            $mygooglereviewsreviews->setProfile_photo_url($review['profile_photo_url']);
            $mygooglereviewsreviews->setRating($review['rating']);
            $mygooglereviewsreviews->setRelative_time_description($review['relative_time_description']);
            $mygooglereviewsreviews->setText($review['text']);
            $mygooglereviewsreviews->setTime($review['time']);
            $mygooglereviewsreviews->setTranslated($review['translated']);

            $em->persist($mygooglereviewsreviews);
            sleep(1);
            
            
        }
        
        $em->flush();

        return $cnt; //$review['profile_photo_url'];

    }
}
