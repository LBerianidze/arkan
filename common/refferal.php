<?php
include_once "init.loader.php";
//$percents = array(15, 8, 7, 6, 5, 4, 3, 2, 1, 1);
//$maxLevel = 10;
function payRefferal($id, $money, $text = "")
{
    global $db;
    $member = $db->getAllRecords('ar_mbrplans', '*', 'and idmbr=' . $id)[0];
    $percents = $db->getAllRecords('ar_payplans', 'referral_bonus', '');
    for ($i = 0; $i < count($percents); $i++)
    {
        $percents[$i] = explode(',', $percents[$i]['referral_bonus']);
    }
    $maxLevel = count($percents[0]);
    include_once $_SERVER['DOCUMENT_ROOT'] . '/Telegram/Sender.php';
    for ($i = 0; $i < $maxLevel; $i++)
    {
        if ($member['idspr'] == 0)
        {
            break;
        }
        $telegram_id = $db->getAllRecords('ar_mbrs', 'telegram_id', 'and id=' . $member['idspr'])[0]['telegram_id'];
        $member = $db->getAllRecords('ar_mbrplans', '*', 'and idmbr=' . $member['idspr'])[0];
        $bonus = ($money * $percents[$member['mppid'] - 1][$i] / 100);
        $mbr = $db->getAllRecords('ar_mbrs', 'ewallet,refwallet', 'and id=' . $member['idmbr'])[0];
        $ewallet = $mbr['ewallet'] + $bonus;
        $refwallet = $mbr['refwallet'] + $bonus;
        $db->update('ar_mbrs', array('ewallet' => $ewallet, 'refwallet' => $refwallet), array('id' => $member['idmbr']));
        sendMessage($telegram_id, "Вам было начислено " . $bonus . ' рублей за пополнение вашим партнером');


        $txdatetm = date('Y-m-d H:i:s');
        $data = array('txdatetm' => $txdatetm, 'txpaytype' => 'manualpayipn', 'txfromid' => $member['idmbr'], 'txtoid' => 0, 'txamount' => $bonus, 'txmemo' => $text . " [" . ($i + 1) . "]", 'txppid' => $plan_id, 'txtoken' => "|PWIDR:IN|, |AMOUNT:{$bonus}|", 'txstatus' => 0, 'txadminfo' => "",);
        $insert = $db->insert(DB_TBLPREFIX . '_transactions', $data);

    }
}
