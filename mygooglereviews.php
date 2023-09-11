<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */
declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

// Needed for install process
require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\ORM\Query\Expr\Func;
use Mygooglereviews\Controller\Admin\SetGoogleReviewsController;
use Mygooglereviews\Controller\Admin\MyTestController;
use Mygooglereviews\Entity\Mygooglereviewsscore;
use Prestashop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class mygooglereviews extends Module implements WidgetInterface
{

    private $templateFile;

    public function __construct()
    {
        $this->name = 'mygooglereviews';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Olivier FOSTIER';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('My Google Reviews');
        $this->description = $this->l('With this module you can handle your Google Reviews from your Google establishment tab. You can then managed your score and retrieve your last 5 reviews and comments associated.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        // if (!Configuration::get('MYBASICMODULE_NAME')) {
        //     $this->warning = $this->l('No name provided');
        // }

        $this->templateFile = 'module:mygooglereviews/score.tpl';
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        // Db action first
        return 
        $this->dbInstall() 
        && parent::install()
        && $this->manuallyInstallTabSet()
        //&& $this->manuallyInstallTabReviews()
        && $this->registerHook('actionFrontControllerSetMedia')
        && $this->registerHook('header')
        //&& $this->registerHook('displayFooter')
        ;
        //&& Configuration::updateValue('MYBASICMODULE_NAME', 'My basic module name');
    }
    public function uninstall()
    {

        return 
        $this->dbUninstall()
        && $this->manuallyuninstallTabSet()
        //&& $this->manuallyuninstallTabReviews()
        && parent::uninstall();
    }


    /**
     * @return bool
     */
    private function manuallyInstallTabSet(): bool
    {
        // Add Tab for ManualTabController
        $controllerClassName = SetGoogleReviewsController::TAB_CLASS_NAME;
        $tabId = (int) Tab::getIdFromClassName($controllerClassName);
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = $controllerClassName;
        $tab->route_name = 'ps_controller_tabs_set';
        $tab->name = [];
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('My Google Reviews', [], 'Modules.Mygooglesreviews.Admin', $lang['locale']);
        }
        $tab->icon = 'build';
        $tab->id_parent = (int) Tab::getIdFromClassName('IMPROVE');
        $tab->module = $this->name;
        

        return (bool) $tab->save();
    }

    public function manuallyuninstallTab(){

        $controllerClassName = ManualTabController::TAB_CLASS_NAME;
        $tabId = (int) Tab::getIdFromClassName($controllerClassName);
        
        if($tabId) {
            $tab = new Tab($tabId);
            try {
                $tab->delete();
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
        return true;
    }

    public function manuallyuninstallTabSet(){

        $controllerClassName = SetGoogleReviewsController::TAB_CLASS_NAME;
        $tabId = (int) Tab::getIdFromClassName($controllerClassName);
        
        if($tabId) {
            $tab = new Tab($tabId);
            try {
                $tab->delete();
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
        return true;
    }

    // CREATE TABLE
    public function dbInstall()
    {

        // $sqlCreate_address = '
        //     CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'mygooglereviews` (
        //     `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        //     `address` varchar(255) DEFAULT NULL,
        //     `placeid` varchar(255) DEFAULT NULL,
        //     PRIMARY KEY (`id`)
        // ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;';

        $sqlCreate_reviews = '
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'mygooglereviewsreviews` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `author_name` varchar(255) DEFAULT NULL,
            `author_url` varchar(255) DEFAULT NULL,
            `language` varchar(255) DEFAULT NULL,
            `original_language` varchar(255) DEFAULT NULL,
            `profile_photo_url` varchar(255) DEFAULT NULL,
            `rating` int(11) DEFAULT NULL,
            `relative_time_description` varchar(255) DEFAULT NULL,
            `text` varchar(255) DEFAULT NULL,
            `time` varchar(255) DEFAULT NULL,
            `translated` varchar(255) DEFAULT NULL,
            `placeid` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;';

        $sqlCreate_scores = '
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'mygooglereviewsscore` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `establishment_id` varchar(255) DEFAULT NULL,
            `establishment_score` int(10) DEFAULT NULL,
            `establishment_nbvote` int(10) DEFAULT NULL,

            PRIMARY KEY (`id`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;';

        return Db::getInstance()->execute($sqlCreate_reviews) && Db::getInstance()->execute($sqlCreate_scores);
        //Db::getInstance()->execute($sqlCreate_reviews) && 
    }

    // DROP Table
    public function dbUninstall()
    {

        $sql_delete_reviews = '
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'mygooglereviewsreviews`;';
        $sql_delete_scores = '
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'mygooglereviewsscore`;';

        return Db::getInstance()->execute($sql_delete_reviews) && Db::getInstance()->execute($sql_delete_scores);
    }   

    public function getContent()
    {
        $output = '';

        // this part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . $this->name)) {
            // retrieve the value set by the user
            $address = (string) Tools::getValue('MYGGOGLEREVIEWS_ADDRESS');
            $token = (string) Tools::getValue('MYGGOGLEREVIEWS_GOOGLE_TOKEN');
            $placeid = (string) Tools::getValue('MYGGOGLEREVIEWS_GOOGLE_PLACEID');

            // check that the value is valid
            if (empty($address) || !Validate::isGenericName($address) || empty($token) || !Validate::isGenericName($token)) {
                // invalid value, show an error
                $output = $this->displayError($this->l('Invalid Configuration value'));
            } else {
                // value is ok, update it and display a confirmation message
                Configuration::updateValue('MYGGOGLEREVIEWS_ADDRESS', $address);
                Configuration::updateValue('MYGGOGLEREVIEWS_GOOGLE_TOKEN', $token);
                Configuration::updateValue('MYGGOGLEREVIEWS_GOOGLE_PLACEID', $placeid);
                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        $placeid = Configuration::get('MYGGOGLEREVIEWS_GOOGLE_PLACEID');

         //$link = new Link();
         $link_get_placeid = $this->context->link->getAdminLink('ps_controller_ajax_get', true, array('route' => 'ps_controller_ajax_get'));
         //$link_refresh_reviews = $this->context->link->getAdminLink('ps_controller_ajax_getreviews', true, array('route' => 'ps_controller_ajax_getreviews'));
         //echo $link_refresh_reviews;
        //Media::addJsDef(['adminlink_get_placeid' => $link_get_placeid, 'adminlink_refresh_reviews' => $link_refresh_reviews]);

        $this->context->controller->addJS($this->_path.'views/js/configuration.js');

        // display any message, then the form

        //var_dump($link_get_placeid);

        return $output . $this->displayForm(Configuration::get('MYGGOGLEREVIEWS_GOOGLE_PLACEID'));
    }

    /**
     * Builds the configuration form
     * @return string HTML code
     */
    public function displayForm($placeid='')
    {
        
        $classdisable = ($placeid == '') ? 'btn btn-danger btn-lg pull-right disabled' : 'btn btn-danger btn-lg pull-right';
        // Init Fields form array
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => [
                    
                    [
                        'type' => 'text',
                        'label' => $this->l('Your establishment address'),
                        'name' => 'MYGGOGLEREVIEWS_ADDRESS',
                        'size' => 255,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Google API TOKEN'),
                        'name' => 'MYGGOGLEREVIEWS_GOOGLE_TOKEN',
                        'size' => 20,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Google place ID'),
                        'name' => 'MYGGOGLEREVIEWS_GOOGLE_PLACEID',
                        'placeholder' => $this->l('Click REFRESH to get your place ID'),
                        'size' => 20,
                        'required' => true,
                        'readonly' => 'true',
                        'attribute' => [
                            'readonly' => 'true'
                        ]
                    ],
                    [
                        'type' => 'hidden',
                        'label' => $this->l('test'),
                        'name' => 'MYGGOGLEREVIEWS_AJAX_ROUTE',
                        'size' => 255,
                        'required' => true,
                        //'value' => $this->context->link->getAdminLink('ps_controller_ajax_get', true, array('route' => 'ps_controller_ajax_get'))
                    ],
                    
                    // [
                    //     'type' => 'text',
                    //     'label' => $this->l('token'),
                    //     'name' => 'gtoken',
                    //     'size' => 255,
                    //     'required' => true,
                    //     //'value' => $this->context->link->getAdminLink('ps_controller_ajax_get', true, array('route' => 'ps_controller_ajax_get'))
                    // ],
                ],
                
                'buttons' => [
                    [
                    'type' => 'button',
                    'title' => $this->l('Get Place Id'),
                    'id' => 'refresh_placeid',
                    'name' => 'refresh_placeid',
                    'class' => 'pull-right btn btn-success btn-lg',
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => $classdisable, //'btn btn-danger btn-lg ', // . ($placeid=='')?' btn btn-danger btn-lg disabled':'',
                ],
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->title = 'TIIIIIIIIIIIIIITLE';
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->toolbar_btn = array (
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => 'gogog'   
            )
        );
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;
        
      // $router = SymphonyContainer::getInstance()->get('router');
       $urltoken = "http://krysak" . $this->context->link->getAdminLink('ps_controller_ajax_get', true, array('route' => 'ps_controller_ajax_get')); //$router->generate('ps_controller_ajax_get'); //

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        $helper->fields_value['MYGGOGLEREVIEWS_AJAX_ROUTE'] = $this->context->link->getAdminLink('ps_controller_ajax_get', true, array('route' => 'ps_controller_ajax_get'));
        $helper->fields_value['MYGGOGLEREVIEWS_ADDRESS'] = Tools::getValue('MYGGOGLEREVIEWS_ADDRESS', Configuration::get('MYGGOGLEREVIEWS_ADDRESS'));
        $helper->fields_value['MYGGOGLEREVIEWS_GOOGLE_TOKEN'] = Tools::getValue('MYGGOGLEREVIEWS_GOOGLE_TOKEN', Configuration::get('MYGGOGLEREVIEWS_GOOGLE_TOKEN'));
        $helper->fields_value['MYGGOGLEREVIEWS_GOOGLE_PLACEID'] = Tools::getValue('MYGGOGLEREVIEWS_GOOGLE_PLACEID', Configuration::get('MYGGOGLEREVIEWS_GOOGLE_PLACEID'));
        //$helper->fields_value['gtoken'] = $urltoken; //*Tools::getAdminTokenLite('SetGoogleReviewsController');; //Tools::getAdminTokenLite('AdminModules');
         
        return $helper->generateForm([$form]);
    }


    public function renderWidget($hookName = null, array $configuration = [])
    {

        $templateFile = 'module:'.$this->name.'/views/templates/widget/score.tpl';
        //$templateFile = $this->templateFile;

        if (!$this->isCached($templateFile, $this->getCacheId('mygooglereviews'))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($templateFile, $this->getCacheId('mygooglereviews'));
    }

    public function getWidgetVariables($hookName , array $configuration)
    {
        //$myParamKey = $configuration['my_param_key'] ?? "null";
        
        $scores = $this->sqlGetScore(Configuration::get('MYGGOGLEREVIEWS_GOOGLE_PLACEID'));
        //var_dump($result['establishment_nbvote']);
        $score = $scores['establishment_score'];
        $nbrating = $scores['establishment_nbvote'];
        
        $reviews = $this->sqlGetReviews(Configuration::get('MYGGOGLEREVIEWS_GOOGLE_PLACEID'));;
        // var_dump($reviews);
        return [
            'score' => number_format((float)$score,1),
            'scorepercent' => 100*($score/5),
            'nbrating' => $nbrating,
            //'css' => $this->_path .'views/css/mygooglereviews_score.css',
            //'css2' => '/modules/' . $this->name . '/views/css/mygooglereviews_score.css',
            'reviews' => $reviews
            //'my_dynamic_var_by_param' => $this->getMyDynamicVarByParamKey($myParamKey),
        ];
    }

    public function sqlGetScore($placeid) {

        $query = new DbQuery();
        $query
            ->select('*')
            ->from('mygooglereviewsscore')
            ->where('establishment_id ="'.$placeid.'"');

        $states = Db::getInstance()->getRow($query);

        return $states;

        $em = $this->container->get('doctrine.orm.entity_manager');
        $mygooglereviewsscore = $em->getRepository(Mygooglereviewsscore::class)->findOneBy(array('establishment_id' => $placeid ));
        
        // $mygooglereviewsscore->setEstablishment_id($placeid);
        // $mygooglereviewsscore->setEstablishment_score($score);
        // $mygooglereviewsscore->setEstablishment_nbvote($nbvotes);

        // $em->persist($mygooglereviewsscore);
        // $em->flush();

        return $mygooglereviewsscore;

        return $mygooglereviewsscore->getId();

    }
    public function sqlGetReviews($placeid) {

        $query = new DbQuery();
        $query
            ->select('*')
            ->from('mygooglereviewsreviews')
            ->where('placeid ="'.$placeid.'"')
            ->orderby('rand()');

        $reviews = Db::getInstance()->executeS($query);

        return $reviews;

        // $em = $this->container->get('doctrine.orm.entity_manager');
        // $mygooglereviewsreviews = $em->getRepository(Mygooglereviewsscore::class)->find(); //By(array('placeid' => $placeid ));
        
        // $mygooglereviewsscore->setEstablishment_id($placeid);
        // $mygooglereviewsscore->setEstablishment_score($score);
        // $mygooglereviewsscore->setEstablishment_nbvote($nbvotes);

        // $em->persist($mygooglereviewsscore);
        // $em->flush();

        return $mygooglereviewsreviews;

        //return $mygooglereviewsscore->getId();

    }
    
    public function getMyDynamicVarByParamKey(string $paramKey)
    {
        if($paramKey === 'my_param_value') {
           return 'my_dynamic_var_by_my_param_value';
        }

        return null;
    }

    /**
     * Assign smarty variables and display the hook
     *
     * @param string $template
     *
     * @return string
     *
     * @throws PrestaShopDatabaseException
     */
    private function renderTemplateInHook($template)
    {
        $id_lang = $this->context->language->id;

        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->display(__FILE__, 'views/templates/hook/score.tpl'); // . $template);
    }

    public function hookActionFrontControllerSetMedia()
    {
        //https://devdocs.prestashop-project.org/8/themes/getting-started/asset-management/
        $this->context->controller->registerStylesheet(
            'mygooglereviews-style',
            'modules/' . $this->name . '/views/css/mygooglereviews.css',
            [
                'media' => 'all',
                'priority' => 1000,
            ]
        );

        // $this->context->controller->registerJavascript(
        //     'mymodule-javascript',
        //     'modules/' . $this->name . '/views/js/mygooglereviews.js',
        //     [
        //         'position' => 'bottom',
        //         'priority' => 1000,
        //     ]
        // );
    }

    public function hookHeader() {

        //$this->context->controller->addCSS($this->_path .'views/css/ps_mygooglereviews_score.css');

        $this->context->controller->registerStylesheet(
            'mygooglereviews_score',
            '/modules/' . $this->name . '/views/css/mygooglereviews_score.css'
        );
    }

}