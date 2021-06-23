<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$genmpid = ($FORM['loadId']) ? $FORM['loadId'] : $mbrRow['mpid'];

$_SESSION['showFltr'] = $FORM['showFltr'] = ($FORM['showFltr'] != '') ? $FORM['showFltr'] : $_SESSION['showFltr'];
$statusmbrsopt = '';
$statusmbrsarr = array('0' => $LANG['g_all'], '1' => $LANG['g_activeonly']);
foreach ($statusmbrsarr as $key => $value) {
    $btnselcolor = ($FORM['showFltr'] == $key) ? 'success' : 'secondary';
    $statusmbrsopt .= "<a href='index.php?hal=genealogyview&showFltr={$key}' class='btn btn-{$btnselcolor}'>{$value}</a>";
}
?>

<link rel="stylesheet" href="../assets/fellow/treant/Treant.css">
<link rel="stylesheet" href="../assets/fellow/treant/simple-scrollbar.css">
<link rel="stylesheet" href="../assets/fellow/treant/perfect-scrollbar.css">

<div class="section-header">
    <h1><i class="fa fa-fw fa-sitemap"></i> <?php echo myvalidate($LANG['m_genealogyview']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo myvalidate($LANG['m_membergenealogy']); ?></h4>
                    <div class="card-header-action">
                        <div class="btn-group">
                            <?php echo myvalidate($statusmbrsopt); ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="genchart" id="genviewer"></div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/fellow/treant/raphael.js"></script>
<script src="../assets/fellow/treant/Treant.js"></script>
<script src="../assets/fellow/treant/jquery.mousewheel.js"></script>
<script src="../assets/fellow/treant/perfect-scrollbar.js"></script>
<script src="loadgenview.php?loadId=<?php echo myvalidate($genmpid); ?>&showFltr=<?php echo myvalidate($FORM['showFltr']); ?>"></script>

<script type="text/javascript">
    new Treant(chart_config);
</script>
