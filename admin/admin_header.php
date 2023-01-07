<?php
include_once '../../../include/cp_header.php';

if (file_exists("../language/{$xoopsConfig['language']}/modinfo.php")) {
    include_once "../language/{$xoopsConfig['language']}/modinfo.php";
} else {
    include_once "../language/english/modinfo.php";
}

//	Get Configulation Data from Database
$CONF = array();
// $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix('logcounterx_cfg')." WHERE cfgvalue = ''");
$sql = "SELECT cfgname, cfgvalue FROM " . $xoopsDB->prefix('logcounterx_cfg');
$res = $xoopsDB->query($sql);
while (list($key, $val) = $xoopsDB->fetchRow($res)) {
    $CONF[$key] = $val;
}

//	Get Count from Database
$sql1 = "SELECT cnt FROM " . $xoopsDB->prefix('logcounterx_count') . " WHERE ymd = '1111-11-11'";
$res1 = $xoopsDB->query($sql1);
list($cnt) = $xoopsDB->fetchRow($res1);
$cnt = (int)($cnt);

//	Unlink (Delete) Block Cache
if (function_exists('glob')) {
    $blockcachefiles = glob(XOOPS_CACHE_PATH . '/blk_*lcx_block_display.html');
    if (!empty($blockcachefiles)) {
        foreach ($blockcachefiles as $f) {
            @unlink($f);
        }
    }
}

xoops_cp_header();

if (is_object($xoopsModule)) {
    $MyModVer  = sprintf('%0.2f', $xoopsModule->getVar('version') / 100);
    $MyModName = $xoopsModule->getVar('name');
}

global $xoopsModuleConfig;

$dash = XOOPS_URL . '/admin.php';

// TODO 
// MODULE does not have 'preferences' but a built-in page with menu !
// Help documentation
// $pref = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=PreferenceEdit&amp;confmod_id=';
$help = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=Help&amp;dirname=logcounterx';

print '<div class="adminnavi"><a href="' . $dash .'">Dashboard</a> »» <span class="adminnaviTitle" aria-current="page"><a href="./index.php">' . $MyModName .'</a></span></div>';
print '<nav class="adminavi">
    <a href="./" class="adminavi-item">Menu</a>
    <a href="' .  $help . '" class="adminavi-item">' . _HELP . '</a>
</nav>';
print    '<h2>' . $MyModName . _LCX_ADM_CONFIG . ' <span class="badge-count" style="font-size:16px;position:relative;bottom:.5em"> ver.' . $MyModVer . '<span></h2>';

if (isset($xoopsModuleConfig['system_menu']) && 1 == $xoopsModuleConfig['system_menu']) {
    //	Display Header Menu
    print    '<h4><a href="./">' . $MyModName . _LCX_ADM_CONFIG . '</a> &nbsp;(LogCounterX ver.' . $MyModVer . ')</h4>';
    print    '<span style="float:right; margin-bottom:16px;">&nbsp;|&nbsp;';
    include './menu.php';
    foreach ($adminmenu as $eachmenu) {
        print '<a href="' . XOOPS_URL . '/modules/logcounterx/' . $eachmenu['link'] . '">' . $eachmenu['title'] . '</a>&nbsp;|&nbsp;';
    }
    print    '</span><br><br>';
}
