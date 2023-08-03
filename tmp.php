<?php

if (!defined('_PS_VERSION_'))
    exit();

class PromotionBanner extends Module
{
    public function __construct()
    {
        $this->name = 'promotionbanner';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'example';
        $this->author_uri = 'https://example.com';
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->dir = '/modules/promotionbanner';
        $this->css_path = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name
            . '/' . $this->_path . 'views/css/';

        parent::__construct();

        $this->displayName = $this->l('Promotion Banner', 'promotionbanner');
        $this->description = $this->l('This module provides configurable promotion banner on your website');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the module?', 'promotionbanner');
    }

    private function updateConf()
    {
        Configuration::updateValue('banner_text', $this->l('Wybrane produkty tańsze o 15%! Kod rabatowy: '));
        Configuration::updateValue('banner_coupon_code', $this->l('Wybierz kupon rabatowy'));
    }

    public function install()
    {
        $this->updateConf();
        return parent::install() && $this -> registerHook('displayWrapperTop') && $this->registerHook('header');
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !Configuration::deleteByName('promotionbanner_module') &&
            !Configuration::deleteByName('banner_coupon_code'))
            return false;
        return true;
    }

    public function hookDisplayWrapperTop($params)
    {
        $this->context->smarty->assign(
            array(
                'banner_text' => Configuration::get('banner_text'),
                'banner_coupon_code' => Configuration::get('banner_coupon_code')
            )
        );

        $this->context->controller->registerStylesheet(
            'modules-promotion-banner2', //This id has to be unique
            'modules/'.$this->name.'/views/css/front.css',
            array('media' => 'all', 'priority' => 150)
        );

        return $this->display(__FILE__, 'promotionbanner.tpl');
    }

    public function hookHeader() {
        $this->context->controller->addCSS($this->_path . 'views/css/front.css', 'all');
        $this->context->controller->registerStylesheet(
            'modules-promotion-banner', //This id has to be unique
            'modules/'.$this->name.'/views/css/front.css',
            array('media' => 'all', 'priority' => 150)
        );
    }

    public function hookActionFrontControllerSetMedia($params) {
        $this->context->controller->registerStylesheet(
            'module-promotionbanner-style',
            'modules/'.$this->name.'/views/css/front.css',
            [
                'media' => 'all',
                'priority' => 200,
            ]
        );
    }

    public function getPromotions()
    {
        $cart_rule = _DB_PREFIX_ . 'cart_rule';
        $request = "SELECT $cart_rule.id_cart_rule, " . _DB_PREFIX_ . "cart_rule_lang.name, $cart_rule.code " .
            "FROM $cart_rule INNER JOIN " . _DB_PREFIX_ . 'cart_rule_lang ON ' . _DB_PREFIX_ . 'cart_rule.id_cart_rule='
            . _DB_PREFIX_ . 'cart_rule_lang.id_cart_rule WHERE ' . _DB_PREFIX_ . 'cart_rule.code IS NOT NULL';
        $db = Db::getInstance();
        $cupons = $db->executeS($request);
        $parsedCupons = array();
        foreach ($cupons as $cupon) {
            array_push($parsedCupons, array(
                'code' => $cupon['code'],
                'name' => $cupon['name']
            ));
        }
        return $parsedCupons;
    }

    public function displayForm()
    {
        $form = $this->renderForm();

        $this->context->smarty->assign(array(
            'banner_text' => Configuration::get('banner_text'),
            'banner_coupon_code' => Configuration::get('banner_coupon_code'),
            'form_url' => AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'name' => $this->name,
            'form_tpl' => $form,
            'coupon_codes' => $this->getPromotions()
        ));
        $this->context->controller->addCSS(array(
            $this->css_path . 'fontawesome-all.min.css',
            $this->css_path . 'module.css'
        ));

        $this->output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/menu.tpl');

        return $this->output;
    }

    public function renderForm()
    {
        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                )
        );

        $helper->fields_value = array(
            'banner_text' => Configuration::get('banner_text'),
            'banner_coupon_code' => Configuration::get('banner_coupon_code')
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    public function getConfigForm()
    {
        $fields_form = array(
            'form' => array(
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Banner text before code: '),
                        'name' => 'banner_text',
                        'lang' => false,
                        'required' => true,
                        'size' => 20
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Coupon code: '),
                        'name' => 'banner_coupon_code',
                        'required' => true,
                        'options' => array(
                            'query' => $this->getPromotions(),
                            'id' => 'code',
                            'name' => 'name'
                        )
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right'
                )
            )
        );

        return $fields_form;
    }

    public function getContent()
    {
        $output = "";

        if (Tools::isSubmit('submit' . $this->name)) {
            $banner_text = strval(Tools::getValue('banner_text'));
            $banner_coupon_code = strval(Tools::getValue('banner_coupon_code'));

            if (!isset($banner_text) || !isset($banner_coupon_code))
                $output .= $this->displayError($this->l('Please insert something in this field.'));
            else {
                Configuration::updateValue('banner_text', $banner_text);
                Configuration::updateValue('banner_coupon_code', $banner_coupon_code);
                $output .= $this->displayConfirmation($this->l('Field updated successfully!'));
            }
        }
        return $output . $this->displayForm();
    }


}