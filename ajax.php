<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

$module_name = 'mygooglereviews';

$token = pSQL(Tools::encrypt($module_name.'/ajax.php'));
$token_url = pSQL(Tools::getValue('token'));

if ($token != $token_url || !Module::isInstalled($module_name)) {
    die('Error when executing ajax');
}

$module = Module::getInstanceByName($module_name);
if ($module->active) {
	$search = pSQL(Tools::getValue('establishment_address'));
    //$search = pSQL(Tools::getValue('establishment_address'));
    if ($search != '') {
        //echo json_encode($module->searchProducts($search));
        //dump($search);
        echo json_encode("ok search ajax");
        //echo json_encode($module->get_google_place('krysakids, mallemort'));
    }
}
