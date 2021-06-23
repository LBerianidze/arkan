<?php
include_once('../../common/init.loader.php');
$seskey = verifylog_sess('member');
if ($seskey == '') {
    exit;
}

$sesRow = getlog_sess($seskey);
$username = get_optionvals($sesRow['sesdata'], 'un');
// Get member details
$mbrstr = getmbrinfo($username, 'username');

extract($_REQUEST);
require_once "Payeer.php";
$config = array();
$config['m_shop'] = '1073201784';
$config['m_key'] = '2Fztj93wZ7LL';
$config['m_encryption_key'] = 'eQ2FDGnhHPYL';
$config['m_amount'] = $amount;
$config['m_curr'] = 'RUB';
$config['m_api_url'] = 'https://payeer.com/merchant/';
$config['m_orderid'] = 4234236;
$config['m_user_id'] = $mbrstr['id'];
$config['page']='fillbalance';
$payeer = new Payeer($config);
echo $payeer->generateForm();