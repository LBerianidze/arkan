<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

// check if already registered to the payplan
if ($mbrstr['idmbr'] != $mbrstr['id']) {
    // not registered
    redirpageto('index.php?hal=planreg');
    exit;
}

// get transaction details
$unpaidtxid = get_unpaidtxid($mbrstr);
if ($unpaidtxid > 0) {
    $txidstr = $unpaidtxid;
    $payforstr = 'ОБНОВЛЕН';
} else {
    $condition = ' AND txtoken LIKE "%|REG:' . $mbrstr['mpid'] . '|%" ';
    $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', '*', $condition);
    $trxstr = array();
    foreach ($row as $value) {
        $trxstr = array_merge($trxstr, $value);
    }
    $txidstr = $trxstr['txid'];
    $payforstr = 'ЗАРЕГИСТРИРОВАН';
}
// -----

$txmpid = $txidstr . '-' . $mbrstr['mpid'];
$regfee = $totpaypal = $totcoinpayments = $totmanualpay = $tottestpay = $mbrstr['reg_fee'];

$paytoken = $payrow['paytoken'];
$isppsandbox = get_optionvals($paytoken, 'paypalsbox');

$ispayg = 0;
$paygatearr = array('paypal', 'coinpayments', 'manualpay', 'testpay');
foreach ($paygatearr as $key => $value) {
    if ($payrow[$value . 'on'] == 1) {
        if ($payrow[$value . 'fee'] > 0) {
            ${'fee' . $value} = getamount($payrow[$value . 'fee'], $regfee);
            ${'tot' . $value} = $regfee + ${'fee' . $value};
        } else {
            ${'fee' . $value} = 0;
        }
        $ispayg++;
    }
}

if ($ispayg <= 1) {
    $colmdclass = "col-md-12";
} elseif ($ispayg <= 2) {
    $colmdclass = "col-md-6";
} else {
    $colmdclass = "col-md-4";
}

$tagsarr = array("[[currencysym]]" => $bpprow['currencysym'], "[[currencycode]]" => $bpprow['currencycode'], "[[feeamount]]" => $feemanualpay, "[[amount]]" => $regfee, "[[totamount]]" => $totmanualpay, "[[payplan]]" => $bpprow['ppname']);
$manualpayipn = base64_decode($payrow['manualpayipn']);
$manualpayipn = strtr($manualpayipn, $tagsarr);
//$manualpayipn64 = base64_encode($manualpayipn . '<button type="button" class="btn btn-warning btn-lg mt-4" onclick=" location.href = \'index.php?hal=feedback&isconfirm=' . base64_encode($txmpid) . '\'">Подтвердить платеж</button>');
$manualpayipn64 = base64_encode($manualpayipn . '<button type="button" class="btn btn-warning btn-lg mt-4" onclick="payTariff()">Подтвердить платеж</button>');
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-money-check"></i> <?php echo myvalidate($LANG['m_planpay']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-12">
            <article class="article article-style-b">
                <div class="article-header">
                    <div class="article-image" data-background="<?php echo myvalidate($planlogo); ?>">
                    </div>
                    <div class="article-badge">
                        <span class="article-badge-item bg-danger">
                            <?php echo myvalidate($bpprow['currencysym'] . $regfee . ' ' . $bpprow['currencycode']); ?>
                        </span>
                        <?php
                        if ($sprstr['mpstatus'] == 1) {
                            ?>
                            <span class="article-badge-item bg-warning">
                                Спонсор <?php echo myvalidate($sprstr['username']); ?>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="article-details">
                    <div class="article-title">
                        <h4><?php echo myvalidate($bpprow['ppname']); ?></h4>
                    </div>
                    <p><?php echo myvalidate($bpprow['planinfo']); ?></p>
                    <div class="article-cta">
                        <span class="badge badge-secondary">
                            <?php echo myvalidate($payforstr); ?>
                        </span>
                        <span class="badge badge-danger">
                            НЕ ОПЛАЧЕН
                        </span>
                    </div>
                </div>
            </article>

        </div>
    </div>

    <h2 class="section-title"><?php echo myvalidate($LANG['m_payoption']); ?></h2>
    <p class="section-lead"><?php echo myvalidate($LANG['m_payinfo']); ?></p>

    <div class="row">
        <?php
        if ($payrow['paypalon'] == 1) {

            $posturl = ($isppsandbox == 1) ? "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr" : "https://ipnpb.paypal.com/cgi-bin/webscr";
            $expday = floatval($bpprow['expday']);
            if ($expday > 0) {
                $cmdstr = '_xclick-subscriptions';
            } else {
                $cmdstr = '_xclick';
            }
            ?>
            <div class="<?php echo myvalidate($colmdclass); ?>">
                <div class="card card-primary">
                    <div class="card-body text-center">
                        <i class="fab fa-paypal fa-fw"></i>
                        <h4>PayPal</h4>
                        <div class="mt-4">Стоимость: <?php echo myvalidate($bpprow['currencysym'] . $regfee); ?></div>
                        <div><code>Service Fee: <?php echo myvalidate($bpprow['currencysym'] . $feepaypal); ?></code></div>
                        <h6>Total: <?php echo myvalidate($bpprow['currencysym'] . $totpaypal . ' ' . $bpprow['currencycode']); ?></h6>
                        <form method="post" action="<?php echo myvalidate($posturl); ?>" id="dopayform">
                            <input type="hidden" name="cmd" value="<?php echo myvalidate($cmdstr); ?>">
                            <?php
                            if ($expday > 0) {
                                ?>
                                <input type="hidden" name="a1" value="0">
                                <input type="hidden" name="p1" value="7">
                                <input type="hidden" name="t1" value="D">
                                <input type="hidden" name="a3" value="<?php echo myvalidate($totpaypal); ?>">
                                <input type="hidden" name="p3" value="<?php echo intval($expday); ?>">
                                <input type="hidden" name="t3" value="D">
                                <input type="hidden" name="src" value="1">
                                <?php
                            } else {
                                ?>
                                <input type="hidden" name="amount" value="<?php echo myvalidate($totpaypal); ?>">
                                <?php
                            }
                            ?>
                            <input type="hidden" name="business" value="<?php echo myvalidate(base64_decode($payrow['paypalacc'])); ?>">
                            <input type="hidden" name="notify_url" value="<?php echo myvalidate($cfgrow['site_url']) . '/common/sandbox.php'; ?>">
                            <input type="hidden" name="return" value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/ipnhub.php?hal=dashboard'; ?>">
                            <input type="hidden" name="cancel_return" value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/index.php?hal=planpay&act=cancelpay'; ?>">
                            <input type="hidden" name="currency_code" value="<?php echo myvalidate($bpprow['currencycode']); ?>">
                            <input type="hidden" name="item_name" value="<?php echo myvalidate($bpprow['ppname']); ?>">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="rm" value="2">
                            <input type="hidden" name="no_shipping" value="1">
                            <input type="hidden" name="no_note" value="1">
                            <input type="hidden" name="custom" value="<?php echo myvalidate($txmpid); ?>">

                            <button type="submit" name="dopay" value="1" id="dopay" class="btn btn-primary btn-lg mt-4">
                                Оплатить
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        if ($payrow['coinpaymentson'] == 1) {
            ?>
            <div class="<?php echo myvalidate($colmdclass); ?>">
                <div class="card card-primary">
                    <div class="card-body text-center">
                        <i class="fa fa-coins fa-fw"></i>
                        <h4>CoinPayments</h4>
                        <div class="mt-4">Стоимость: <?php echo myvalidate($bpprow['currencysym'] . $regfee); ?></div>
                        <div><code>Service Fee: <?php echo myvalidate($bpprow['currencysym'] . $feecoinpayments); ?></code></div>
                        <h6>Total: <?php echo myvalidate($bpprow['currencysym'] . $totcoinpayments . ' ' . $bpprow['currencycode']); ?></h6>
                        <form method="post" action="https://www.coinpayments.net/index.php" id="dopayform">
                            <input type="hidden" name="cmd" value="_pay_simple"> <!-- or _pay -->
                            <input type="hidden" name="reset" value="1">
                            <input type="hidden" name="merchant" value="<?php echo myvalidate(base64_decode($payrow['coinpaymentsmercid'])); ?>">
                            <input type="hidden" name="item_name" value="<?php echo myvalidate($bpprow['ppname']); ?>">
                            <input type="hidden" name="item_number" value="<?php echo myvalidate($mbrstr['username']); ?>">
                            <input type="hidden" name="invoice" value="<?php echo myvalidate($txmpid); ?>">
                            <input type="hidden" name="currency" value="<?php echo myvalidate($bpprow['currencycode']); ?>">
                            <input type="hidden" name="amountf" value="<?php echo myvalidate($totcoinpayments); ?>">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="allow_quantity" value="1">
                            <input type="hidden" name="want_shipping" value="0">
                            <input type="hidden" name="success_url" value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/ipnhub.php?hal=dashboard'; ?>">
                            <input type="hidden" name="cancel_url" value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/index.php?hal=planpay&act=cancelpay'; ?>">
                            <input type="hidden" name="ipn_url" value="<?php echo myvalidate($cfgrow['site_url']) . '/common/sandbox.php'; ?>">
                            <input type="hidden" name="allow_extra" value="1">

                            <button type="submit" name="dopay" value="1" id="dopay" class="btn btn-primary btn-lg mt-4">
                                Оплатить
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        if ($payrow['manualpayon'] == 1) {
            ?>
            <div class="<?php echo myvalidate($colmdclass); ?>">
                <div class="card card-primary">
                    <div class="card-body text-center">
                        <i class="fa fa-handshake fa-fw"></i>
                        <h4><?php echo myvalidate($payrow['manualpayname']); ?></h4>
                        <div class="mt-4">Стоимость: <?php echo myvalidate($bpprow['currencysym'] . $regfee); ?></div>
                        <div><code>Комиссия: <?php echo myvalidate($bpprow['currencysym'] . $feemanualpay); ?></code></div>
                        <h6>Сумма: <?php echo myvalidate($bpprow['currencysym'] . $totmanualpay . ' ' . $bpprow['currencycode']); ?></h6>
                        <button type="button" class="openPopup btn btn-primary btn-lg mt-4" data-encbase64="<?php echo myvalidate($manualpayipn64); ?>" data-poptitle="<i class='fa fa-fw fa-handshake'></i> <?php echo myvalidate($payrow['manualpayname']); ?>">
                            Оплатить
                        </button>
                    </div>
                </div>
            </div>
            <?php
        }
        if ($payrow['testpayon'] == 1) {
            $paybatch = strtoupper(date("DmdH-is")) . $mbrstr['mpid'];
            ?>
            <div class="<?php echo myvalidate($colmdclass); ?>">
                <div class="card card-danger">
                    <div class="card-body text-center">
                        <i class="fa fa-cog fa-fw"></i>
                        <h4><?php echo myvalidate($payrow['testpaylabel']); ?></h4>
                        <div class="mt-4">Стоимость: <?php echo myvalidate($bpprow['currencysym'] . $regfee); ?></div>
                        <div><code>Комиссия: <?php echo myvalidate($bpprow['currencysym'] . $feetestpay); ?></code></div>
                        <h6>Total: <?php echo myvalidate($bpprow['currencysym'] . $tottestpay . ' ' . $bpprow['currencycode']); ?></h6>
                        <div class="mt-4"><?php echo myvalidate($LANG['m_testpayinfo']); ?></div>
                        <form method="post" action="../common/sandbox.php" id="dopayform">
                            <input type="hidden" name="sb_type" value="payreg">
                            <input type="hidden" name="sb_txmpid" value="<?php echo myvalidate($txmpid); ?>">
                            <input type="hidden" name="sb_amount" value="<?php echo myvalidate($tottestpay); ?>">
                            <input type="hidden" name="sb_batch" value="<?php echo myvalidate($paybatch); ?>">
                            <input type="hidden" name="sb_label" value="<?php echo myvalidate($payrow['testpaylabel']); ?>">
                            <input type="hidden" name="sb_success" value="<?php echo myvalidate($cfgrow['site_url']) . '/' . MBRFOLDER_NAME . '/ipnhub.php?hal=dashboard'; ?>">
                            <button type="submit" name="dopay" value="1" id="dopay" class="btn btn-danger btn-lg mt-4">
                                Оплатить
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
