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

//use PrestaShop\Module\MyGoogleReviews\Controller\Admin\ConfigureController;
use Mygooglereviews\Controller\Admin\ManualTabController;
use Mygooglereviews\Controller\Admin\SetGoogleReviewsController;
use Prestashop\PrestaShop\Adapter\SymfonyContainer;

//use PrestaShop\Module\DemoControllerTabs\Controller\Admin\ConfigureController;

class mygooglereviews extends Module
{
    //const TAB_CLASS_OFO = 'AdminMygooglereviewsSetGoogleReviews';

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
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        // Db action first
        return 
        $this->dbInstall() 
        //&& $this->manuallyInstallTab()
        && $this->manuallyInstallTabSet()
        && parent::install();
        //&& Configuration::updateValue('MYBASICMODULE_NAME', 'My basic module name');
    }
    public function uninstall()
    {

        return parent::uninstall()
        //&& $this->unregisterHook('backOfficeHeader')
        && $this->dbUninstall()
        && $this->manuallyuninstallTab()
        && $this->manuallyuninstallTabSet();
    }

    /**
     * @return bool
     */
    private function manuallyInstallTab(): bool
    {
        // Add Tab for ManualTabController
        $controllerClassName = ManualTabController::TAB_CLASS_NAME;
        $tabId = (int) Tab::getIdFromClassName($controllerClassName);
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = $controllerClassName;
        $tab->route_name = 'ps_controller_tabs_manual_tab';
        $tab->name = [];
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('My Google Reviews', [], 'Modules.Mygooglesreviews.Admin', $lang['locale']);
        }
        $tab->icon = 'build';
        $tab->id_parent = (int) Tab::getIdFromClassName('IMPROVE');
        $tab->module = $this->name;
        

        return (bool) $tab->save();
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
        // $tabId = (int)Tab::getIdFromClassName('AdminMyGoogleReviews');
        
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
        // $tabId = (int)Tab::getIdFromClassName('AdminMyGoogleReviews');
        
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

}