<?php
if (!defined('OK_LOADME'))
{
    die('o o p s !');
}

$newsprstr = '';
$ceknewmpid = getmpidflow($sesref['mpid']);
if ($ceknewmpid != $sesref['mpid'])
{
    $sesnewref = getmbrinfo('', '', $ceknewmpid);
    $newsprstr = "<blockquote class='text-primary text-left'>Вы приглашены <strong>{$sesref['username']}</strong>, который достиг максимального количества рефералов. Система присвоила <strong>{$sesnewref['username']}</strong> статус вашего спонсора.</blockquote>";
    $idref = $sesnewref['id'];
}

if ($bpprow['planstatus'] == 1 && $FORM['doid'] > 0)
{
    regmbrplans($mbrstr, $sesref['mpid'], $FORM['doid']);
    redirpageto('index.php?hal=planreg');
    exit;
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-unlock-alt"></i> <?php echo myvalidate($LANG['m_planreg']); ?></h1>
</div>

<div class="section-body">
    <div class="row" id="subscribePlans">
        <div class="col-md-12">
            <article class="article article-style-b">
                <div class="article-header">
                    <div class="article-image" data-background="<?php echo myvalidate($planlogo); ?>">
                    </div>
                    <div class="article-badge">
                        <span class="article-badge-item bg-danger">
                            <?php echo myvalidate($bpprow['currencysym'] . ' ' . $payplans[0]['regfee'] . ' ' . $bpprow['currencycode']); ?>
                        </span>
                        <?php
                        if ($sesref['username'] && $mbrstr['username'] != $sesref['username'])
                        {
                            ?>
                            <span class="article-badge-item bg-warning">
                                Приглашен <?php echo  myvalidate($sesref['username']); ?>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="article-details">
                    <div class="article-title">
                        <h4><?php echo myvalidate($payplans[0]['ppname']); ?></h4>
                    </div>
                    <p><?php echo myvalidate($payplans[0]['planinfo']); ?></p>
                    <div class="article-cta">
                        <?php echo myvalidate($newsprstr); ?>

                        <?php
                        if ($mbrstr['mppid'] == 1)
                        {
                            if ($mbrstr['idmbr'] == $mbrstr['id'] && $mbrstr['mpstatus'] > 0)
                            {
                                ?>
                                <span class="badge badge-secondary">
                                ЗАРЕГИСТРИРОВАН
                            </span>
                                <?php
                                if ($mbrstr['mpstatus'] == 1)
                                {
                                    ?>
                                    <span class="badge badge-success">
                                    АКТИВНЫЙ <i class="fas fa-fw fa-check"></i>
                                </span>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <span class="badge badge-warning">
                                    НЕАКТИВНЫЙ <i class="fas fa-fw fa-exclamation"></i>
                                </span>
                                    <?php
                                }
                                ?>
                                <?php
                            }
                        }
                        else if (!isset($mbrstr['mppid']))
                        {
                            $refbystr = ($sesref['username']) ? "Вас пригласил <strong>{$sesref['username']}</strong><br />" : '';
                            $refbystr .= ($sesnewref['username']) ? "Ваш спонсор <strong>{$sesnewref['username']}</strong><br />" : '';
                            ?>
                            <a href="javascript:;"
                               data-href="index.php?hal=planreg&doid=<?php echo myvalidate($bpprow['ppid']); ?>"
                               class="btn btn-lg btn-primary bootboxconfirm"
                               data-poptitle="<?php echo myvalidate($bpprow['ppname']); ?> - <?php echo myvalidate($bpprow['currencysym'] . $bpprow['regfee'] . ' ' . $bpprow['currencycode']); ?>"
                               data-popmsg="<?php echo myvalidate($refbystr); ?><p>Вы действительно хотите зарегистрироваться??</p>">Регистрация
                                <i class="fas fa-fw fa-long-arrow-alt-right"></i></a>
                            <?php
                        }
                        if ($mbrstr['idmbr'] == $mbrstr['id'] && $mbrstr['mpstatus'] == 0)
                        {
                            ?>
                            <button type="button" class="btn btn-warning btn-lg mt-4" onclick="payTariff(1)">Подтвердить
                                платеж
                            </button>
                                <!-- <a href="index.php?hal=planpay&doid=<?php /*echo myvalidate($bpprow['ppid']); */
                            ?>"
                                   class="btn btn-lg btn-danger">СДЕЛАТЬ ПЛАТЕЖ <i
                                            class="fas fa-fw fa-long-arrow-alt-right"></i></a>
                                --><?php
                        }
                        ?>

                    </div>
                </div>
            </article>

        </div>
    </div>
    <div id="dopayform" class="row">
        <div class="col-md-12">
            <article class="article article-style-b">
                <div class="article-header">
                    <div class="article-image" data-background="<?php echo myvalidate($planlogo); ?>">
                    </div>
                    <div class="article-badge">
                        <span class="article-badge-item bg-danger">
                            <?php echo myvalidate($bpprow['currencysym'] . ' ' . $payplans[1]['regfee'] . ' ' . $bpprow['currencycode']); ?>
                        </span>
                        <?php
                        if ($sesref['username'] && $mbrstr['username'] != $sesref['username'])
                        {
                            ?>
                            <span class="article-badge-item bg-warning">
                                Приглашен <?php echo  myvalidate($sesref['username']); ?>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="article-details">
                    <div class="article-title">
                        <h4><?php echo myvalidate($payplans[1]['ppname']); ?></h4>
                    </div>
                    <p><?php echo myvalidate($payplans[1]['planinfo']); ?></p>
                    <div class="article-cta">
                        <?php
                        if (isset($mbrstr['mppid']) && $mbrstr['mppid'] < 2 && $mbrstr['mpstatus'] == 1)
                        {
                            ?>
                            <button class="btn btn-success btn-round"
                                    onclick="moveToTariff(2)"><?= myvalidate($LANG['g_changesubscription']) ?></button>
                            <?php
                        }
                        else
                        {
                            ?>
                            <?php echo myvalidate($newsprstr); ?>

                            <?php
                            if ($mbrstr['mppid'] == 2)
                            {
                                if ($mbrstr['idmbr'] == $mbrstr['id'] && $mbrstr['mpstatus'] > 0)
                                {
                                    ?>
                                    <span class="badge badge-secondary">
                                ЗАРЕГИСТРИРОВАН
                            </span>
                                    <?php
                                    if ($mbrstr['mpstatus'] == 1)
                                    {
                                        ?>
                                        <span class="badge badge-success">
                                    АКТИВНЫЙ <i class="fas fa-fw fa-check"></i>
                                </span>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <span class="badge badge-warning">
                                    НЕАКТИВНЫЙ <i class="fas fa-fw fa-exclamation"></i>
                                </span>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if (isset($mbrstr['mppid']) && $mbrstr['mppid'] < 2 && $mbrstr['mpstatus'] == 1)
                                    {
                                        ?>
                                        <a href="index.php?hal=planreg" class="btn btn-success btn-round">Перейти на
                                            этот
                                            пакет</a>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                }
                            }
                            else if (!isset($mbrstr['mppid']))
                            {
                                $refbystr = ($sesref['username']) ? "Вас пригласил <strong>{$sesref['username']}</strong><br />" : '';
                                $refbystr .= ($sesnewref['username']) ? "Ваш спонсор <strong>{$sesnewref['username']}</strong><br />" : '';
                                ?>
                                <a href="javascript:;"
                                   data-href="index.php?hal=planreg&doid=<?php echo myvalidate($payplans[1]['ppid']); ?>"
                                   class="btn btn-lg btn-primary bootboxconfirm"
                                   data-poptitle="<?php echo myvalidate($payplans[1]['ppname']); ?> - <?php echo myvalidate($bpprow['currencysym'] . $payplans[1]['regfee'] . ' ' . $bpprow['currencycode']); ?>"
                                   data-popmsg="<?php echo myvalidate($refbystr); ?><p>Вы действительно хотите зарегистрироваться??</p>">Регистрация
                                    <i class="fas fa-fw fa-long-arrow-alt-right"></i></a>
                                <?php
                            }
                            if ($mbrstr['idmbr'] == $mbrstr['id'] && $mbrstr['mpstatus'] == 0)
                            {
                                ?>
                                <button type="button" class="btn btn-warning btn-lg mt-4" onclick="payTariff(2)">
                                    Подтвердить платеж
                                </button>
                                <?php
                            }
                            ?>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </article>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <article class="article article-style-b">
                <div class="article-header">
                    <div class="article-image" data-background="<?php echo myvalidate($planlogo); ?>">
                    </div>
                    <div class="article-badge">
                        <span class="article-badge-item bg-danger">
                            <?php echo myvalidate($bpprow['currencysym'] . ' ' . $payplans[2]['regfee'] . ' ' . $bpprow['currencycode']); ?>
                        </span>
                        <?php
                        if ($sesref['username'] && $mbrstr['username'] != $sesref['username'])
                        {
                            ?>
                            <span class="article-badge-item bg-warning">
                                Приглашен <?php echo  myvalidate($sesref['username']); ?>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="article-details">
                    <div class="article-title">
                        <h4><?php echo myvalidate($payplans[2]['ppname']); ?></h4>
                    </div>
                    <p><?php echo myvalidate($payplans[2]['planinfo']); ?></p>
                    <div class="article-cta">
                        <?php
                        if (isset($mbrstr['mppid']) && $mbrstr['mppid'] < 3 && $mbrstr['mpstatus'] == 1)
                        {
                            ?>
                            <button class="btn btn-success btn-round"
                                    onclick="moveToTariff(3)"><?= myvalidate($LANG['g_changesubscription']) ?></button>
                            <?php
                        }
                        else
                        {
                            ?>
                            <?php echo myvalidate($newsprstr); ?>

                            <?php
                            if ($mbrstr['mppid'] == 3)
                            {
                                if ($mbrstr['idmbr'] == $mbrstr['id'] && $mbrstr['mpstatus'] > 0)
                                {
                                    ?>
                                    <span class="badge badge-secondary">
                                ЗАРЕГИСТРИРОВАН
                            </span>
                                    <?php
                                    if ($mbrstr['mpstatus'] == 1)
                                    {
                                        ?>
                                        <span class="badge badge-success">
                                    АКТИВНЫЙ <i class="fas fa-fw fa-check"></i>
                                </span>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <span class="badge badge-warning">
                                    НЕАКТИВНЫЙ <i class="fas fa-fw fa-exclamation"></i>
                                </span>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if (isset($mbrstr['mppid']) && $mbrstr['mppid'] < 3 && $mbrstr['mpstatus'] == 1)
                                    {
                                        ?>
                                        <a href="index.php?hal=planreg" class="btn btn-success btn-round">Перейти на
                                            этот
                                            пакет</a>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                }
                            }
                            else if (!isset($mbrstr['mppid']))
                            {
                                $refbystr = ($sesref['username']) ? "Вас пригласил <strong>{$sesref['username']}</strong><br />" : '';
                                $refbystr .= ($sesnewref['username']) ? "Ваш спонсор <strong>{$sesnewref['username']}</strong><br />" : '';
                                ?>
                                <a href="javascript:;"
                                   data-href="index.php?hal=planreg&doid=<?php echo myvalidate($payplans[2]['ppid']); ?>"
                                   class="btn btn-lg btn-primary bootboxconfirm"
                                   data-poptitle="<?php echo myvalidate($payplans[2]['ppname']); ?> - <?php echo myvalidate($bpprow['currencysym'] . $payplans[2]['regfee'] . ' ' . $bpprow['currencycode']); ?>"
                                   data-popmsg="<?php echo myvalidate($refbystr); ?><p>Вы действительно хотите зарегистрироваться??</p>">Регистрация
                                    <i class="fas fa-fw fa-long-arrow-alt-right"></i></a>
                                <?php
                            }
                            if ($mbrstr['idmbr'] == $mbrstr['id'] && $mbrstr['mpstatus'] == 0)
                            {
                                ?>
                                <button type="button" class="btn btn-warning btn-lg mt-4" onclick="payTariff(3)">
                                    Подтвердить платеж
                                </button>
                                <!-- <a href="index.php?hal=planpay&doid=<?php /*echo myvalidate($payplans[2]['ppid']); */
                                ?>"
                                       class="btn btn-lg btn-danger">СДЕЛАТЬ ПЛАТЕЖ <i
                                                class="fas fa-fw fa-long-arrow-alt-right"></i></a>-->
                                <?php
                            }
                            ?>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </article>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <article class="article article-style-b">
                <div class="article-header">
                    <div class="article-image" data-background="<?php echo myvalidate($planlogo); ?>">
                    </div>
                    <div class="article-badge">
                        <span class="article-badge-item bg-danger">
                            <?php echo myvalidate($bpprow['currencysym'] . ' ' . $payplans[3]['regfee'] . ' ' . $bpprow['currencycode']); ?>
                        </span>
                        <?php
                        if ($sesref['username'] && $mbrstr['username'] != $sesref['username'])
                        {
                            ?>
                            <span class="article-badge-item bg-warning">
                                Приглашен <?php echo  myvalidate($sesref['username']); ?>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="article-details">
                    <div class="article-title">
                        <h4><?php echo myvalidate($payplans[3]['ppname']); ?></h4>
                    </div>
                    <p><?php echo myvalidate($payplans[3]['planinfo']); ?></p>
                    <div class="article-cta">
                        <?php
                        if (isset($mbrstr['mppid']) && $mbrstr['mppid'] < 4 && $mbrstr['mpstatus'] == 1)
                        {
                            ?>
                            <button href="index.php?hal=planreg" class="btn btn-success btn-round"
                                    onclick="moveToTariff(4)"><?= myvalidate($LANG['g_changesubscription']) ?></button>
                            <?php
                        }
                        else
                        {
                            ?>
                            <?php echo myvalidate($newsprstr); ?>

                            <?php
                            if ($mbrstr['mppid'] == 4)
                            {
                                if ($mbrstr['idmbr'] == $mbrstr['id'] && $mbrstr['mpstatus'] > 0)
                                {
                                    ?>
                                    <span class="badge badge-secondary">
                                ЗАРЕГИСТРИРОВАН
                            </span>
                                    <?php
                                    if ($mbrstr['mpstatus'] == 1)
                                    {
                                        ?>
                                        <span class="badge badge-success">
                                    АКТИВНЫЙ <i class="fas fa-fw fa-check"></i>
                                </span>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <span class="badge badge-warning">
                                    НЕАКТИВНЫЙ <i class="fas fa-fw fa-exclamation"></i>
                                </span>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if (isset($mbrstr['mppid']) && $mbrstr['mppid'] < 4 && $mbrstr['mpstatus'] == 1)
                                    {
                                        ?>
                                        <a href="index.php?hal=planreg" class="btn btn-success btn-round">Перейти на
                                            этот
                                            пакет</a>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                }
                            }
                            else if (!isset($mbrstr['mppid']))
                            {
                                $refbystr = ($sesref['username']) ? "Вас пригласил <strong>{$sesref['username']}</strong><br />" : '';
                                $refbystr .= ($sesnewref['username']) ? "Ваш спонсор <strong>{$sesnewref['username']}</strong><br />" : '';
                                ?>
                                <a href="javascript:;"
                                   data-href="index.php?hal=planreg&doid=<?php echo myvalidate($payplans[3]['ppid']); ?>"
                                   class="btn btn-lg btn-primary bootboxconfirm"
                                   data-poptitle="<?php echo myvalidate($payplans[3]['ppname']); ?> - <?php echo myvalidate($bpprow['currencysym'] . $payplans[3]['regfee'] . ' ' . $bpprow['currencycode']); ?>"
                                   data-popmsg="<?php echo myvalidate($refbystr); ?><p>Вы действительно хотите зарегистрироваться??</p>">Регистрация
                                    <i class="fas fa-fw fa-long-arrow-alt-right"></i></a>
                                <?php
                            }
                            if ($mbrstr['idmbr'] == $mbrstr['id'] && $mbrstr['mpstatus'] == 0)
                            {
                                ?>
                                <button type="button" class="btn btn-warning btn-lg mt-4" onclick="payTariff(4)">
                                    Подтвердить платеж
                                </button>
                                <!-- <a href="index.php?hal=planpay&doid=<?php /*echo myvalidate($payplans[2]['ppid']); */
                                ?>"
                                       class="btn btn-lg btn-danger">СДЕЛАТЬ ПЛАТЕЖ <i
                                                class="fas fa-fw fa-long-arrow-alt-right"></i></a>-->
                                <?php
                            }
                            ?>
                            <?php
                        }
                        ?>

                    </div>
                </div>
            </article>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <article class="article article-style-b">
                <div class="article-header">
                    <div class="article-image" data-background="<?php echo myvalidate($planlogo); ?>">
                    </div>
                    <div class="article-badge">
                        <span class="article-badge-item bg-danger">
                            <?php echo myvalidate($bpprow['currencysym'] . ' ' . $payplans[4]['regfee'] . ' ' . $bpprow['currencycode']); ?>
                        </span>
                        <?php
                        if ($sesref['username'] && $mbrstr['username'] != $sesref['username'])
                        {
                            ?>
                            <span class="article-badge-item bg-warning">
                                Приглашен <?php echo  myvalidate($sesref['username']); ?>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="article-details">
                    <div class="article-title">
                        <h4><?php echo myvalidate($payplans[4]['ppname']); ?></h4>
                    </div>
                    <p><?php echo myvalidate($payplans[4]['planinfo']); ?></p>
                    <div class="article-cta">
                        <?php
                        if (isset($mbrstr['mppid']) && $mbrstr['mppid'] < 5 && $mbrstr['mpstatus'] == 1)
                        {
                            ?>
                            <button class="btn btn-success btn-round"
                                    onclick="moveToTariff(5)"><?= myvalidate($LANG['g_changesubscription']) ?></button>
                            <?php
                        }
                        else
                        {
                            ?>
                            <?php echo myvalidate($newsprstr); ?>

                            <?php
                            if ($mbrstr['mppid'] == 5)
                            {
                                if ($mbrstr['idmbr'] == $mbrstr['id'] && $mbrstr['mpstatus'] > 0)
                                {
                                    ?>
                                    <span class="badge badge-secondary">
                                ЗАРЕГИСТРИРОВАН
                            </span>
                                    <?php
                                    if ($mbrstr['mpstatus'] == 1)
                                    {
                                        ?>
                                        <span class="badge badge-success">
                                    АКТИВНЫЙ <i class="fas fa-fw fa-check"></i>
                                </span>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <span class="badge badge-warning">
                                    НЕАКТИВНЫЙ <i class="fas fa-fw fa-exclamation"></i>
                                </span>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                }
                            }
                            else if (!isset($mbrstr['mppid']))
                            {
                                $refbystr = ($sesref['username']) ? "Вас пригласил <strong>{$sesref['username']}</strong><br />" : '';
                                $refbystr .= ($sesnewref['username']) ? "Ваш спонсор <strong>{$sesnewref['username']}</strong><br />" : '';
                                ?>
                                <a href="javascript:;"
                                   data-href="index.php?hal=planreg&doid=<?php echo myvalidate($payplans[4]['ppid']); ?>"
                                   class="btn btn-lg btn-primary bootboxconfirm"
                                   data-poptitle="<?php echo myvalidate($payplans[4]['ppname']); ?> - <?php echo myvalidate($bpprow['currencysym'] . $payplans[4]['regfee'] . ' ' . $bpprow['currencycode']); ?>"
                                   data-popmsg="<?php echo myvalidate($refbystr); ?><p>Вы действительно хотите зарегистрироваться??</p>">Регистрация
                                    <i class="fas fa-fw fa-long-arrow-alt-right"></i></a>
                                <?php
                            }
                            if ($mbrstr['idmbr'] == $mbrstr['id'] && $mbrstr['mpstatus'] == 0)
                            {
                                ?>
                                <button type="button" class="btn btn-warning btn-lg mt-4" onclick="payTariff(5)">
                                    Подтвердить платеж
                                </button>
                                <!-- <a href="index.php?hal=planpay&doid=<?php /*echo myvalidate($payplans[2]['ppid']); */
                                ?>"
                                       class="btn btn-lg btn-danger">СДЕЛАТЬ ПЛАТЕЖ <i
                                                class="fas fa-fw fa-long-arrow-alt-right"></i></a>-->
                                <?php
                            }
                            ?>
                            <?php
                        }
                        ?>

                    </div>
                </div>
            </article>

        </div>
    </div>
</div>
