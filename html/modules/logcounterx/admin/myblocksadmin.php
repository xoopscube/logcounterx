<?php
// ------------------------------------------------------------------------- //
//                            myblocksadmin.php                              //
//                - XOOPS block admin for each modules -                     //
//                          GIJOE <http://www.peak.ne.jp/>                   //
// ------------------------------------------------------------------------- //

//include_once( '../../../include/cp_header.php' ) ;
include_once('./admin_header.php');

if (substr(XOOPS_VERSION, 6, 3) > 2.0) {
    include 'myblocksadmin2.php';
    exit;
}

include_once('mygrouppermform.php');
include_once(XOOPS_ROOT_PATH . '/class/xoopsblock.php');
include_once "../include/gtickets.php";// GIJ


$xoops_system_path = XOOPS_ROOT_PATH . '/modules/system';

// language files
$language = $xoopsConfig['language'];
if (!file_exists("$xoops_system_path/language/$language/admin/blocksadmin.php")) {
    $language = 'english';
}

// to prevent from notice that constants already defined
$error_reporting_level = error_reporting(0);
include_once("$xoops_system_path/constants.php");
include_once("$xoops_system_path/language/$language/admin.php");
include_once("$xoops_system_path/language/$language/admin/blocksadmin.php");
error_reporting($error_reporting_level);

$group_defs = file("$xoops_system_path/language/$language/admin/groups.php");
foreach ($group_defs as $def) {
    if (strstr($def, '_AM_ACCESSRIGHTS') || strstr($def, '_AM_ACTIVERIGHTS')) {
        eval($def);
    }
}

// check $xoopsModule
if (!is_object($xoopsModule)) {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}

// set target_module if specified by $_GET['dirname']
$module_handler =& xoops_gethandler('module');
if (!empty($_GET['dirname'])) {
    $target_module =& $module_handler->getByDirname($_GET['dirname']);
}/* else if( ! empty( $_GET['mid'] ) ) {
    $target_module =& $module_handler->get( (int) ( $_GET['mid'] ) );
}*/

if (!empty($target_module) && is_object($target_module)) {
    // specified by dirname
    $target_mid     = $target_module->getVar('mid');
    $target_mname   = $target_module->getVar('name') . "&nbsp;" . sprintf("(%2.2f)", $target_module->getVar('version') / 100.0);
    $query4redirect = '?dirname=' . urlencode(strip_tags($_GET['dirname']));
} else {
    if (isset($_GET['mid']) && $_GET['mid'] == 0 || $xoopsModule->getVar('dirname') == 'blocksadmin') {
        $target_mid     = 0;
        $target_mname   = '';
        $query4redirect = '?mid=0';
    } else {
        $target_mid     = $xoopsModule->getVar('mid');
        $target_mname   = $xoopsModule->getVar('name');
        $query4redirect = '';
    }
}

// check access right (needs system_admin of BLOCK)
$sysperm_handler =& xoops_gethandler('groupperm');
if (!$sysperm_handler->checkRight('system_admin', XOOPS_SYSTEM_BLOCK, $xoopsUser->getGroups())) {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}

// get blocks owned by the module (Imported from xoopsblock.php then modified)
//$block_arr =& XoopsBlock::getByModule( $target_mid ) ;
$db        =& XoopsDatabaseFactory::getDatabaseConnection();
$sql       = "SELECT * FROM " . $db->prefix("newblocks") . " WHERE mid='$target_mid' ORDER BY visible DESC,side,weight";
$result    = $db->query($sql);
$block_arr = array();
while ($myrow = $db->fetchArray($result)) {
    $block_arr[] = new XoopsBlock($myrow);
}

function list_blocks()
{
    global $query4redirect, $block_arr, $xoopsGTicket;

    // cachetime options
    $cachetimes = array('0' => _NOCACHE, '30' => sprintf(_SECONDS, 30), '60' => _MINUTE, '300' => sprintf(_MINUTES, 5), '1800' => sprintf(_MINUTES, 30), '3600' => _HOUR, '18000' => sprintf(_HOURS, 5), '86400' => _DAY, '259200' => sprintf(_DAYS, 3), '604800' => _WEEK, '2592000' => _MONTH);

    // displaying TH
    echo "
	<form action='admin.php' name='blockadmin' method='post'>
		<table class='outer'>
		<tr valign='middle'>
			<th>" . _AM_TITLE . "</th>
			<th align='center' nowrap='nowrap'>" . _AM_SIDE . "</th>
			<th align='center'>" . _AM_WEIGHT . "</th>
			<th align='center'>" . _AM_VISIBLEIN . "</th>
			<th align='center'>" . _AM_BCACHETIME . "</th>
			<th align='right'>" . _AM_ACTION . "</th>
		</tr>\n";

    // blocks displaying loop
    $class         = 'even';
    $block_configs = get_block_configs();
    foreach (array_keys($block_arr) as $i) {
        $sseln = $ssel0 = $ssel1 = $ssel2 = $ssel3 = $ssel4 = "";
        $scoln = $scol0 = $scol1 = $scol2 = $scol3 = $scol4 = "#FFFFFF";

        $weight     = $block_arr[$i]->getVar("weight");
        $title      = $block_arr[$i]->getVar("title");
        $name       = $block_arr[$i]->getVar("name");
        $bcachetime = $block_arr[$i]->getVar("bcachetime");
        $bid        = $block_arr[$i]->getVar("bid");

        // visible and side
        if ($block_arr[$i]->getVar("visible") != 1) {
            $sseln = " checked='checked'";
            $scoln = "#FF0000";
        } else {
            switch ($block_arr[$i]->getVar("side")) {
            default :
            case XOOPS_SIDEBLOCK_LEFT :
                $ssel0 = " checked='checked'";
                $scol0 = "#00FF00";
                break;
            case XOOPS_SIDEBLOCK_RIGHT :
                $ssel1 = " checked='checked'";
                $scol1 = "#00FF00";
                break;
            case XOOPS_CENTERBLOCK_LEFT :
                $ssel2 = " checked='checked'";
                $scol2 = "#00FF00";
                break;
            case XOOPS_CENTERBLOCK_RIGHT :
                $ssel4 = " checked='checked'";
                $scol4 = "#00FF00";
                break;
            case XOOPS_CENTERBLOCK_CENTER :
                $ssel3 = " checked='checked'";
                $scol3 = "#00FF00";
                break;
        }
        }

        // bcachetime
        $cachetime_options = '';
        foreach ($cachetimes as $cachetime => $cachetime_name) {
            if ($bcachetime == $cachetime) {
                $cachetime_options .= "<option value='$cachetime' selected='selected'>$cachetime_name</option>\n";
            } else {
                $cachetime_options .= "<option value='$cachetime'>$cachetime_name</option>\n";
            }
        }

        // target modules
        $db            =& XoopsDatabaseFactory::getDatabaseConnection();
        $result        = $db->query("SELECT module_id FROM " . $db->prefix('block_module_link') . " WHERE block_id='$bid'");
        $selected_mids = array();
        while (list($selected_mid) = $db->fetchRow($result)) {
            $selected_mids[] = (int)($selected_mid);
        }
        $module_handler =& xoops_gethandler('module');
        $criteria       = new CriteriaCompo(new Criteria('hasmain', 1));
        $criteria->add(new Criteria('isactive', 1));
        $module_list     =& $module_handler->getList($criteria);
        $module_list[-1] = _AM_TOPPAGE;
        $module_list[0]  = _AM_ALLPAGES;
        ksort($module_list);
        $module_options = '';
        foreach ($module_list as $mid => $mname) {
            if (in_array($mid, $selected_mids)) {
                $module_options .= "<option value='$mid' selected='selected'>$mname</option>\n";
            } else {
                $module_options .= "<option value='$mid'>$mname</option>\n";
            }
        }

        // delete link if it is cloned block
        $delete_link = '';
        if ($block_arr[$i]->getVar("block_type") == 'D' || $block_arr[$i]->getVar("block_type") == 'C') {
            $delete_link = "<br><a href='admin.php?fct=blocksadmin&amp;op=delete&amp;bid=$bid'>" . _DELETE . "</a>";
        }

        // clone link if it is marked as cloneable block
        // $modversion['blocks'][n]['can_clone']
        if ($block_arr[$i]->getVar("block_type") == 'D' || $block_arr[$i]->getVar("block_type") == 'C') {
            $can_clone = true;
        } else {
            $can_clone = false;
            foreach ($block_configs as $bconf) {
                if ($block_arr[$i]->getVar("show_func") == $bconf['show_func'] && $block_arr[$i]->getVar("func_file") == $bconf['file'] && (empty($bconf['template']) || $block_arr[$i]->getVar("template") == $bconf['template'])) {
                    if (!empty($bconf['can_clone'])) {
                        $can_clone = true;
                    }
                }
            }
        }
        $clone_link = '';
        if ($can_clone) {
            $clone_link = "<br><a href='admin.php?fct=blocksadmin&amp;op=clone&amp;bid=$bid'>" . _CLONE . "</a>";
        }

        // displaying part
        echo "
		<tr valign='middle'>
			<td class='$class'>
				$name
				<br>
				<input type='text' name='title[$bid]' value='$title' size='20' />
			</td>
			<td class='$class' align='center' nowrap='nowrap' width='125px'>
				<div style='float:left;background-color:$scol0;'>
					<input type='radio' name='side[$bid]' value='" . XOOPS_SIDEBLOCK_LEFT . "' style='background-color:$scol0;' $ssel0 />
				</div>
				<div style='float:left;'>-</div>
				<div style='float:left;background-color:$scol2;'>
					<input type='radio' name='side[$bid]' value='" . XOOPS_CENTERBLOCK_LEFT . "' style='background-color:$scol2;' $ssel2 />
				</div>
				<div style='float:left;background-color:$scol3;'>
					<input type='radio' name='side[$bid]' value='" . XOOPS_CENTERBLOCK_CENTER . "' style='background-color:$scol3;' $ssel3 />
				</div>
				<div style='float:left;background-color:$scol4;'>
					<input type='radio' name='side[$bid]' value='" . XOOPS_CENTERBLOCK_RIGHT . "' style='background-color:$scol4;' $ssel4 />
				</div>
				<div style='float:left;'>-</div>
				<div style='float:left;background-color:$scol1;'>
					<input type='radio' name='side[$bid]' value='" . XOOPS_SIDEBLOCK_RIGHT . "' style='background-color:$scol1;' $ssel1 />
				</div>
				<br>
				<br>
				<div style='float:left;width:40px;'>&nbsp;</div>
				<div style='float:left;background-color:$scoln;'>
					<input type='radio' name='side[$bid]' value='-1' style='background-color:$scoln;' $sseln />
				</div>
				<div style='float:left;'>" . _NONE . "</div>
			</td>
			<td class='$class' align='center'>
				<input type='text' name=weight[$bid] value='$weight' size='3' maxlength='5' style='text-align:right;' />
			</td>
			<td class='$class' align='center'>
				<select name='bmodule[$bid][]' size='5' multiple='multiple'>
					$module_options
				</select>
			</td>
			<td class='$class' align='center'>
				<select name='bcachetime[$bid]' size='1'>
					$cachetime_options
				</select>
			</td>
			<td class='$class' align='right'>
				<a href='admin.php?fct=blocksadmin&amp;op=edit&amp;bid=$bid'>" . _EDIT . "</a>{$delete_link}{$clone_link}
				<input type='hidden' name='bid[$bid]' value='$bid' />
			</td>
		</tr>\n";

        $class = ($class == 'even') ? 'odd' : 'even';
    }

    echo "
		<tr>
			<td class='foot' align='center' colspan='6'>
				<input type='hidden' name='query4redirect' value='$query4redirect' />
				<input type='hidden' name='fct' value='blocksadmin' />
				<input type='hidden' name='op' value='order' />
				" . $xoopsGTicket->getTicketHtml(__LINE__, 1800, 'myblocksadmin') . "
				<input type='submit' name='submit' value='" . _SUBMIT . "' />
			</td>
		</tr>
		<tr>
			<td class='foot' align='center' colspan='6'>
			<a href=\"./setup_count_block.php\">" . _LCX_ADM_CUPBLK_SET . "</a>
			</td>
		</tr>
		</table>
	</form>\n";
}

/**
 * @return array
 */
function get_block_configs()
{
    $error_reporting_level = error_reporting(0);
    include '../xoops_version.php';
    error_reporting($error_reporting_level);
    if (empty($modversion['blocks'])) {
        return array();
    } else {
        return $modversion['blocks'];
    }
}

function list_groups()
{
    global $target_mid, $target_mname, $block_arr;

    $item_list = array();
    foreach (array_keys($block_arr) as $i) {
        $item_list[$block_arr[$i]->getVar("bid")] = $block_arr[$i]->getVar("title");
    }

    $form = new MyXoopsGroupPermForm(_MD_AM_ADGS, 1, 'block_read', '');
    if ($target_mid > 1) {
        $form->addAppendix('module_admin', $target_mid, $target_mname . ' ' . _AM_ACTIVERIGHTS);
        $form->addAppendix('module_read', $target_mid, $target_mname . ' ' . _AM_ACCESSRIGHTS);
    }
    foreach ($item_list as $item_id => $item_name) {
        $form->addItem($item_id, $item_name);
    }
    echo $form->render();
}

if (!empty($_POST['submit'])) {
    if (!$xoopsGTicket->check(true, 'myblocksadmin')) {
        redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
    }

    include("mygroupperm.php");
    redirect_header(XOOPS_URL . "/modules/" . $xoopsModule->dirname() . "/admin/myblocksadmin.php$query4redirect", 1, _MD_AM_DBUPDATED);
}

//xoops_cp_header() ;
if (file_exists('./mymenu.php')) {
    include('./mymenu.php');
}

// echo "<h3 style='text-align:left;'>$target_mname</h3>\n" ;

if (!empty($block_arr)) {
    // echo "<h4 style='text-align:left;'>"._AM_BADMIN."</h4>\n" ;
    echo "<table width=\"100%\"><tr><th>" . _AM_BADMIN . "</th></tr></table>\n";
    list_blocks();
}

list_groups();
//xoops_cp_footer() ;
include_once './admin_footer.php';
