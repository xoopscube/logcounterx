<?php
/**
 * LogCounterX module for XCL
 * @package    LogCounterX
 * @version    XCL 2.3.1
 * @author     Other authors Gigamaster, 2022 XCL PHP7.x
 * @author     Other authors Miraldo Ohse aka Ohsepoa
 * @author     (c) 2005 taquino.net
 * @copyright  (c) 2005-2022 Authors
 * @license    https://github.com/xoopscube/legacy/blob/master/GPL_V2.txt
 */

$dirname   = basename( dirname( __FILE__ ) );

// Manifesto
$modversion['dirname']          = "logcounterx";
$modversion['name']             = _LCX_MI_NAME;
$modversion['version']          = '2.80';
$modversion['detailed_version'] = '2.80.1';
$modversion['description']      = _LCX_MI_DESC;
$modversion['author']           = "Taquino - xoops.taquino.net";
$modversion['credits']          = "Miraldo Ohse aka Ohsepoa, Gigamaster XCL 2.3.x";
$modversion['license']          = 'GPL';
$modversion['image']            = 'images/module_logcounterx.svg';
$modversion['icon']             = 'images/module_icon.svg';
$modversion['help']             = 'help.html';
$modversion['official']         = 0;
$modversion['cube_style']       = false;
$modversion['read_any']         = true;



// Install
$modversion['onInstall']        = 'admin/lcx_install.php';
$modversion['onUninstall']      = 'admin/lcx_uninstall.php';
$modversion['onUpdate']         = 'admin/lcx_update.php';

// Template 
$modversion['templates'][1]['file'] = 'report.html';
$modversion['templates'][1]['description'] = 'Log Report';

// SQL 
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][0]        = "logcounterx_ip";
$modversion['tables'][1]        = "logcounterx_count";
$modversion['tables'][2]        = "logcounterx_log";
$modversion['tables'][3]        = "logcounterx_cfg";
$modversion['tables'][4]        = "logcounterx_hours";

$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu']  = "admin/menu.php";

$modversion['hasMain'] = 1;

$modversion['blocks'][1]['file']        = "display.php";
$modversion['blocks'][1]['name']        = _LCX_MI_CTR_NAME;
$modversion['blocks'][1]['description'] = _LCX_MI_CTR_DESC;
$modversion['blocks'][1]['show_func']   = "b_logcounterx_show_counter";
$modversion['blocks'][1]['template']    = 'lcx_block_display.html';
$modversion['blocks'][1]['options']     = '';

$modversion['blocks'][2]['file']        = "count_up.php";
$modversion['blocks'][2]['name']        = _LCX_MI_INC_NAME;
$modversion['blocks'][2]['description'] = _LCX_MI_INC_DESC;
$modversion['blocks'][2]['show_func']   = "b_logcounterx_inc_counter";
$modversion['blocks'][2]['options']     = '';




// keeping block's options when module is updated
if (!empty($_POST['fct']) && !empty($_POST['op']) && $_POST['fct'] == 'modulesadmin' && $_POST['op'] == 'update_ok' && $_POST['dirname'] == $modversion['dirname']) {
    include __DIR__ . "/include/onupdate.inc.php";
}
