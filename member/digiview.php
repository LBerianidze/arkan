<?php
if (!defined('OK_LOADME'))
{
    die('o o p s !');
}

if (isset($FORM['pgid']))
{
    $pgid = mystriptag($FORM['pgid']);
    $row = $db->getAllRecords(DB_TBLPREFIX . "_pages", "*", " AND pgid = '{$pgid}' AND (pglang = '' OR pglang = '{$mbrstr['mylang']}')");
    $pgcntrow = array();
    foreach ($row as $value)
    {
        $pgcntrow = array_merge($pgcntrow, $value);
    }

    if (!iscontentmbr($pgcntrow['pgavalon'], $mbrstr) || $pgcntrow['pgstatus'] != 1)
    {
        $pgcntrow['pgtitle'] = "Мы не смогли найти никаких данных";
        $pgcntrow['pgsubtitle'] = $pgcntrow['pgcontent'] = '';
    }
    else
    {
        $pgcntrow['pgsubtitle'] = base64_decode($pgcntrow['pgsubtitle']);
        $pgcntrow['pgcontent'] = base64_decode($pgcntrow['pgcontent']);
    }
}

$msgListData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_pages WHERE 1 AND (pglang = '' OR pglang = '{$mbrstr['mylang']}')");

$noviewpage = <<<INI_HTML
                <div class="empty-state">
                    <div class="empty-state-icon bg-info">
                        <i class="fas fa-question"></i>
                    </div>
                    <h2>Мы не смогли найти ни одной страницы</h2>
                    <p class="lead">
                        Извините, мы не можем найти контент для вас
                    </p>
                </div>
INI_HTML;
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-window-restore"></i> <?php echo myvalidate($LANG['a_digicontent']); ?></h1>
</div>

<div class="section-body">
    <?php
    if ($mbrstr['mpstatus'] == 0 || $mbrstr['subscription_active'] == 0 || (new DateTime()) > (new DateTime($mbrstr['subscription_end_date']))){
    ?>
        <div class="card-header text-danger">
            <h4><?php echo 'Необходимо оплатить тариф или абонентскую плату' ?></h4>
        </div>
    <?php
    }
    else
    {
        ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo myvalidate($LANG['g_content']); ?></h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <?php
                        if (count($msgListData) > 0)
                        {
                            $numpage = 0;
                            foreach ($msgListData as $val)
                            {
                                if (!iscontentmbr($val['pgavalon'], $mbrstr) || $val['pgstatus'] != 1)
                                {
                                    continue;
                                }
                                $strsel = ($FORM['pgid'] == $val['pgid']) ? ' selected' : '';
                                $pagelink = "index.php?hal=digiview&pgid={$val['pgid']}";
                                ?>
                                <button type="button" class="btn btn-info mt-2"
                                        onclick="location.href = '<?php echo myvalidate($pagelink); ?>'"><?php echo isset($val['pgmenu']) ? $val['pgmenu'] : '?'; ?></button>
                                <?php
                                $numpage++;
                            }
                            if ($numpage < 1)
                            {
                                echo "Записей не найдено!";
                            }
                            else
                            {
                                $noviewpage = '<i class="fa fa-fw fa-long-arrow-alt-left"></i> ' . $LANG['m_clicklefttocnt'];
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">

                <div class="card-header">
                    <h4><?php echo myvalidate($pgcntrow['pgtitle']); ?></h4>
                </div>

                <div class="card-body">
                    <p class="text-muted"><?php echo ($FORM['pgid'] != '') ? "<div class='section-title mt-2'>{$pgcntrow['pgsubtitle']}</div>" : $noviewpage; ?></p>

                    <?php
                    if ($FORM['pgid'] != '')
                    {
                        echo isset($pgcntrow['pgcontent']) ? $pgcntrow['pgcontent'] : '';
                    }
                    ?>

                </div>

            </div>
        </div>
    </div>
    <?php
    }
    ?>
</div>
