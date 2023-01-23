<?php

include_once "admin_header.php";

$myts =& MyTextSanitizer::getInstance();

//	Update Database
if (isset($_POST['command'])) {
    if ($_POST['command'] == 'RESETCOUNT') {
        $cnt = (int)($_POST['count']);
        $sql = "SELECT cnt FROM " . $xoopsDB->prefix('logcounterx_count') . " WHERE ymd = '1111-11-11'";
        $res = $xoopsDB->query($sql);
        if ($xoopsDB->getrowsNum($res) == 0) {
            $sql1 = "INSERT INTO " . $xoopsDB->prefix('logcounterx_count') . " (ymd, cnt) VALUES ('1111-11-11', $cnt)";
            $res1 = $xoopsDB->query($sql1);
        } else {
            $sql1 = "UPDATE " . $xoopsDB->prefix("logcounterx_count") . " SET cnt = $cnt WHERE ymd = '1111-11-11'";
            $res1 = $xoopsDB->query($sql1);
        }
    }
    if ($_POST['command'] == 'TIMEOFFSET') {
        if ($CONF['TIME_OFFSET'] != (int)($_POST['TIME_OFFSET'])) {
            $CONF['TIME_OFFSET'] = (int)($_POST['TIME_OFFSET']);
            $sql                 = "UPDATE " . $xoopsDB->prefix("logcounterx_cfg") .
                                   " SET cfgvalue = '${CONF['TIME_OFFSET']}' WHERE cfgname = 'TIME_OFFSET'";
            $res                 = $xoopsDB->query($sql);
            $sql                 = "DELETE FROM " . $xoopsDB->prefix("logcounterx_ip");
            $res                 = $xoopsDB->query($sql);
        }
        $sql = "UPDATE " . $xoopsDB->prefix("logcounterx_hours") . " SET cnt = 0, robot = 0";
        $res = $xoopsDB->query($sql);
    }
    if ($_POST['command'] == 'REPROBOT') {
        $CONF['REP_ROBOT'] = (int)($_POST['REP_ROBOT']);
        $sql               = "UPDATE " . $xoopsDB->prefix("logcounterx_cfg") .
                             " SET cfgvalue = '${CONF['REP_ROBOT']}' WHERE cfgname = 'REP_ROBOT'";
        $res               = $xoopsDB->query($sql);
    }
    if (($_POST['command'] == 'ADDIP') && ($_POST['addr'] != '')) {
        $sql = "SELECT cfgvalue FROM " . $xoopsDB->prefix("logcounterx_cfg") .
               " WHERE (cfgname = 'IGNORE_HOST') AND (cfgvalue = '" . addslashes($myts->stripSlashesGPC($_POST['addr'])) . "')";
        if ($xoopsDB->getrowsNum($xoopsDB->query($sql)) == 0) {
            $sql1 = "INSERT INTO " . $xoopsDB->prefix("logcounterx_cfg") .
                    " (cfgname, cfgvalue) VALUES ('IGNORE_HOST', '" . addslashes($myts->stripSlashesGPC($_POST['addr'])) . "')";
            $res1 = $xoopsDB->query($sql1);
        }
    }
    if ($_POST['command'] == 'DELETEIP') {
        for ($i = 1; $i <= (int)($_POST['count']); ++$i) {
            if (isset($_POST['mark' . $i]) && ($_POST['mark' . $i] == 'on')) {
                $sql = "DELETE FROM " . $xoopsDB->prefix("logcounterx_cfg") .
                       " WHERE recid = " . (int)($_POST['id' . $i]) . " AND cfgname = 'IGNORE_HOST'";
                $res = $xoopsDB->query($sql);
            }
        }
    }
    if (($_POST['command'] == 'ADDREF') && ($_POST['referer'] != '')) {
        $sql = "SELECT cfgvalue FROM " . $xoopsDB->prefix("logcounterx_cfg") .
               " WHERE (cfgname = 'IGNORE_REFERER') AND (cfgvalue = '" . addslashes($myts->stripSlashesGPC($_POST['referer'])) . "')";
        if ($xoopsDB->getrowsNum($xoopsDB->query($sql)) == 0) {
            $sql1 = "INSERT INTO " . $xoopsDB->prefix("logcounterx_cfg") .
                    " (cfgname, cfgvalue) VALUES ('IGNORE_REFERER', '" . addslashes($myts->stripSlashesGPC($_POST['referer'])) . "')";
            $res1 = $xoopsDB->query($sql1);
        }
    }
    if ($_POST['command'] == 'DELETEREF') {
        for ($i = 1; $i <= (int)($_POST['count']); ++$i) {
            if (isset($_POST['mark' . $i]) && ($_POST['mark' . $i] == 'on')) {
                $sql = "DELETE FROM " . $xoopsDB->prefix("logcounterx_cfg") .
                       " WHERE recid = " . (int)($_POST['id' . $i]) . " AND cfgname = 'IGNORE_REFERER'";
                $res = $xoopsDB->query($sql);
            }
        }
    }
    if ($_POST['command'] == 'LOGCFG') {
        foreach (array('NO_ROBOT_COUNT', 'NO_HOST_COUNT', 'USE_GET_HOST', 'USER_COOKIE') as $key) {
            if (isset($_POST[$key]) && ($_POST[$key] != $CONF[$key])) {
                $CONF[$key] = (int)($_POST[$key]);
                $sql        = "UPDATE " . $xoopsDB->prefix('logcounterx_cfg') . " SET cfgvalue = '${CONF[$key]}'" .
                              " WHERE cfgname = '$key'";
                $res        = $xoopsDB->query($sql);
            }
        }
    }
}

//	Get Count
//	$sql = "SELECT cnt FROM " . $xoopsDB->prefix("logcounterx_count") . " WHERE ymd = '1111-11-11'";
//	list($cnt) = $xoopsDB->fetchRow($xoopsDB->query($sql));
$sql = "SELECT MAX(acccnt) FROM " . $xoopsDB->prefix("logcounterx_log");
list($AccCnt) = $xoopsDB->fetchRow($xoopsDB->query($sql));

//	form - Reset Total Count
print '<form name="form1" method="post" action="logadm.php">
<table class="outer"><thead><tr><th colspan="2">' . _LCX_ADM_LOGCONF . '</th></tr></thead>
<tr><td>' . _LCX_ADM_RESETCOUNT_TITLE . '<br><br><small>' . _LCX_ADM_RESETCOUNT_DESC . '</small></td>
<td>
<input type="text" size="12" name="count" value="' . $cnt . '">
<input type="hidden" name="command" value="RESETCOUNT">
<input type="submit" value="' . _SUBMIT . '" name="save"><br>
' . (int)($AccCnt) . '  ' . _LCX_ADM_LOGCOUNT_TITLE . '
</td></tr></table></form>
<hr>';

//	form - Time Offset
print '<form name="form11" method="post" action="logadm.php">
<table class="outer"><thead><tr><th colspan="2">Time Offset</th></tr></thead>
<tr>
<td>' . _LCX_ADM_TIMEOFFSET_NAME . '</td>
<td><input type="text" size="12" name="TIME_OFFSET" value="' . $CONF['TIME_OFFSET'] . '"></td>
</tr>
<tr>
<td><small>' . _LCX_ADM_TIMEOFFSET_DESC . '</small></td>
<td>
<span class="badge">' . date('Y-m-d H:i:s', time()) . '</span> ' . _LCX_ADM_TIMEOFFSET_SVTM . '<br>
<span class="badge">' . date('Y-m-d H:i:s', time() + $CONF['TIME_OFFSET'] * 60 * 60) . '</span> ' . _LCX_ADM_TIMEOFFSET_ADTM .'
</td>
</tr>
<tfoot>
<tr><td></td><td><input type="hidden" name="command" value="TIMEOFFSET">
<input type="submit" value="' . _SUBMIT . '" name="save"><br> </td></tr></tfoot></table></form>
<hr>';

//	form - CountUp & GethostByAddr
print '<form name="form2" method="post" action="logadm.php">
<table class="outer"><thead><tr><th colspan="2">Count-Up and Get host By Address</th></tr></thead>';
print_input_area('NO_ROBOT_COUNT', $CONF['NO_ROBOT_COUNT'], _LCX_ADM_NOROBCNT_NAME, _LCX_ADM_NOROBCNT_DESC, 'yesno');
print_input_area('NO_HOST_COUNT', $CONF['NO_HOST_COUNT'], _LCX_ADM_NOHSTCNT_NAME, _LCX_ADM_NOHSTCNT_DESC, 'yesno');
// print_input_area ('USER_COOKIE',	$CONF['USER_COOKIE'],	_LCX_ADM_USER_COOKIE_NAME,	_LCX_ADM_USER_COOKIE_DESC,	'yesno');
print_input_area('USE_GET_HOST', $CONF['USE_GET_HOST'], _LCX_ADM_GETHOST_NAME, _LCX_ADM_GETHOST_DESC, 'yesno');
print '<tfoot><tr><td></td>
<td>
<input type="hidden" name="command" value="LOGCFG">
<input type="submit" value="' . _SUBMIT . '" name="save"><br>
</td>
</tr></tfoot>
</table>
</form>
<hr>';

//	form - Report ROBOT
print '<form name="form2" method="post" action="logadm.php">
<table class="outer"><thead><tr><th colspan="2">Report ROBOT</th></tr></thead>
<tr>
<td>' . _LCX_ADM_REPORTING_TITLE . '<br><br>
<small>' . _LCX_ADM_REPORTING_DESC . '</small></td>
<td class="even">
<input type="radio" name="REP_ROBOT" value="0"' . (($CONF['REP_ROBOT'] == 0) ? ' style="background-color:#00FF00;" checked="checked"' : '') . ' />' . _LCX_ADM_REPORTING_ALL . '
<input type="radio" name="REP_ROBOT" value="1"' . (($CONF['REP_ROBOT'] == 1) ? ' style="background-color:#00FF00;" checked="checked"' : '') . ' />' . _LCX_ADM_REPORTING_WORBT . '
<input type="radio" name="REP_ROBOT" value="2"' . (($CONF['REP_ROBOT'] == 2) ? ' style="background-color:#00FF00;" checked="checked"' : '') . ' />' . _LCX_ADM_REPORTING_ROBOT . '
</td>
</tr>
<tfoot><tr><td></td>
<td><input type="hidden" name="command" value="REPROBOT">
<input type="submit" value="' . _SUBMIT . '" name="save"></td></tr></tfoot>
</table></form>
<hr>';

//	form - Add Ignore Host
print '<form name="form2" method="post" action="logadm.php">
<table class="outer"><thead><tr><th colspan="2" height="3">Add Ignore Host</th></tr></thead>
<tr>
<td>' . _LCX_ADM_ADDIP_TITLE . '<br><br>
<small>' . _LCX_ADM_ADDIP_DESC . '</small></td>
<td>
<input type="text" size="60" name="addr">
<input type="hidden" name="command" value="ADDIP">
<input type="submit" value="' . _ADD . '" name="save">
</td>
</tr></table></form><hr>';

//	form - Your Host & Put it into Ignore Host List ?
if (!isset($_SERVER['REMOTE_HOST'])) {
    $_SERVER['REMOTE_HOST'] = $_SERVER['REMOTE_ADDR'];
}
print    '<table class="outer"><tr><td class="head">' . _LCX_ADM_YOURHOST_TITLE . '</td>';

$YourHost = addslashes($myts->stripSlashesGPC($_SERVER['REMOTE_HOST']));
$sql      = "SELECT cfgvalue FROM " . $xoopsDB->prefix("logcounterx_cfg") .
            " WHERE (cfgname = 'IGNORE_HOST') AND ('$YourHost' like cfgvalue)";

if ($xoopsDB->getrowsNum($xoopsDB->query($sql)) == 0) {
    print    '<form method="post" action="logadm.php">' .
             '<td class="even">' . htmlspecialchars($_SERVER['REMOTE_HOST']) . '&nbsp;&nbsp;&nbsp;' .
             '<input type="hidden" name="addr" value="' . htmlspecialchars($_SERVER['REMOTE_HOST']) . '">' .
             '<input type="hidden" name="command" value="ADDIP">' .
             '<input type="submit" value="' . _LCX_ADM_YOURHOST_ADD . '">' .
             '</td>' .
             '</form>';
} else {
    print '<td class="even">' . htmlspecialchars($_SERVER['REMOTE_HOST']) . '</td>';
}
print '</tr>';

//	form - Ignore Hosts List & Delete them ?
$i   = 0;
$sql = "SELECT recid, cfgvalue FROM " . $xoopsDB->prefix("logcounterx_cfg") .
       " WHERE (cfgname = 'IGNORE_HOST') ORDER BY cfgvalue";
$res = $xoopsDB->query($sql);
if (($xoopsDB->getrowsNum($res)) > 0) {
    print    '<tr><td class="head">' .
             '<form name="form3" method="post" action="logadm.php">' .
             _LCX_ADM_DELETEIP_TITLE . '<br><br>' .
             '<small>' . _LCX_ADM_DELETEIP_DESC . '</small></td>' .
             '<td class="even"><table>';
    while (list($RecID, $IgHost) = $xoopsDB->fetchRow($res)) {
        ++$i;
        $RecID  = (int)($RecID);
        $IgHost = $IgHost;
        print    '<tr><td><input type="checkbox" name="mark' . $i . '" value="on"></td>' .
                 '<td><input type="hidden" name="id' . $i . '" value="' . $RecID . '">' . htmlspecialchars($IgHost) . "</td></tr>";
    }
    print    '</table>' .
             '<input type="hidden" name="count" value="' . $i . '">' .
             '<input type="hidden" name="command" value="DELETEIP">' .
             '<input type="submit" name="save" value="' . _DELETE . '">' .
             '</form></td></tr>';
}
print '</table><hr>';

//	form - Add Ignore Referer
print '<form name="form2" method="post" action="logadm.php">
<table class="outer"><thead><tr><th colspan="2">Add Ignore Referer</th></tr></thead>
<tr>
<td class="head">' . _LCX_ADM_ADDREF_TITLE . '<br><br>
<small>' . _LCX_ADM_ADDREF_DESC . '</small></td>
<td class="even">
<input type="text" size="60" name="referer">
<input type="hidden" name="command" value="ADDREF">
<input type="submit" value="' . _ADD . '" name="save">
</td>
</tr></table></form><hr>';

//	form - Ignore Referrers List & Delete them ?
$i   = 0;
$sql = "SELECT recid, cfgvalue FROM " . $xoopsDB->prefix("logcounterx_cfg") .
       " WHERE (cfgname = 'IGNORE_REFERER') ORDER BY cfgvalue";
$res = $xoopsDB->query($sql);

print '<form name="form3" method="post" action="logadm.php">
<table class="outer"><thead><tr><th colspan="2">Ignored Referer</th></tr></thead>';

if (($xoopsDB->getrowsNum($res)) > 0) {
    print '<tr><td class="head">' . _LCX_ADM_DELETEREF_TITLE . '<br><br>' .
          '<small>' . _LCX_ADM_DELETEREF_DESC . '</small></td>' .
          '<td class="even">' .
          '<table>';
    while (list($RecID, $IgReferer) = $xoopsDB->fetchRow($res)) {
        ++$i;
        $RecID  = (int)($RecID);
        $IgHost = $IgReferer;
        print '<tr><td><input type="checkbox" name="mark' . $i . '" value="on"></td>' .
              '<td><input type="hidden" name="id' . $i . '" value="' . $RecID . '">' . htmlspecialchars($IgReferer) . "</td></tr>";
    }
    print '</table>' .
          '</td></tr>' .
          '<tfoot><td><input type="hidden" name="count" value="' . $i . '">' .
          '<input type="hidden" name="command" value="DELETEREF"></td>' .
          '<td><input type="submit" name="save" value="' . _DELETE . '"></td>' .
          '</tr></tfoot>';
}
print '</table></form>';

include_once "admin_footer.php";

/**
 * @param $nam
 * @param $val
 * @param $ttl
 * @param $dsc
 * @param $typ
 * @return int
 */
function print_input_area($nam, $val, $ttl, $dsc, $typ)
{
    print    '<tr><td class="head">' . $ttl . '<br><br>' .
             '<small>' . $dsc . '</small></td><td class="even">';
    if ($typ == 'yesno') {
        if ($val) {
            print    '<input type="radio" name="' . $nam . '" value="1" style="background-color:#00FF00;" checked="checked">' . _YES;
            print    '<input type="radio" name="' . $nam . '" value="0">' . _NO;
        } else {
            print    '<input type="radio" name="' . $nam . '" value="1">' . _YES;
            print    '<input type="radio" name="' . $nam . '" value="0" style="background-color:#00FF00;" checked="checked">' . _NO;
        }
    } else {
        print    '<input type="text" size="12" name="' . $nam . '" value="' . $val . '">';
    }
    print    '</td></tr>';

    return 1;
}
