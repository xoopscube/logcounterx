<?php

include_once "./admin_header.php";

$fld = array('REP_R2', 'REP_QW', 'REP_OS', 'REP_BR', 'REP_RC', 'REP_DR', 'REP_WD', 'REP_TM', 'REP_HN', 'REP_UN', 'REP_RF', 'REP_PI', 'REP_LINK');
$nam = array(
    'REP_R2' => _LCX_ADM_BY_R2, 'REP_QW' => _LCX_ADM_BY_QW, 'REP_OS' => _LCX_ADM_BY_OS,
    'REP_BR' => _LCX_ADM_BY_BR, 'REP_RC' => _LCX_ADM_BY_RC, 'REP_DR' => _LCX_ADM_BY_DR,
    'REP_WD' => _LCX_ADM_BY_WD, 'REP_TM' => _LCX_ADM_BY_TM, 'REP_HN' => _LCX_ADM_BY_HN,
    'REP_UN' => _LCX_ADM_BY_UN, 'REP_RF' => _LCX_ADM_BY_RF, 'REP_LINK' => _LCX_ADM_REFLINK,
    'REP_PI' => _LCX_ADM_BY_PI);

if (isset($_POST['command']) && ($_POST['command'] == 'edit')) {
    $sql = "UPDATE " . $xoopsDB->prefix("logcounterx_cfg") . " SET cfgvalue = '" . (int)($_POST['REP_ROWLIMIT']) . "'" .
           " WHERE cfgname = 'REP_ROWLIMIT'";
    $res = $xoopsDB->queryF($sql);
    $sql = "UPDATE " . $xoopsDB->prefix("logcounterx_cfg") . " SET cfgvalue = '" . (int)($_POST['REP_ROWLIMIT_ADM']) . "'" .
           " WHERE cfgname = 'REP_ROWLIMIT_ADM'";
    $res = $xoopsDB->queryF($sql);
    $sql = "UPDATE " . $xoopsDB->prefix("logcounterx_cfg") . " SET cfgvalue = '" . (int)($_POST['REP_ROWLIMIT_USR']) . "'" .
           " WHERE cfgname = 'REP_ROWLIMIT_USER'";
    $res = $xoopsDB->queryF($sql);
    foreach ($fld as $f) {
        if ($_POST[$f] != $CONF[$f]) {
            $sql = "UPDATE " . $xoopsDB->prefix("logcounterx_cfg") . " SET cfgvalue = '" . (int)($_POST[$f]) .
                   "' WHERE cfgname = '$f'";
            $res = $xoopsDB->queryF($sql);
        }
    }
}

$opt = array(_LCX_ADM_GUEST, _LCX_ADM_USERS, _LCX_ADM_ADMIN, _LCX_ADM_NOONE);

$sql = "SELECT cfgname, cfgvalue FROM " . $xoopsDB->prefix("logcounterx_cfg") . " WHERE cfgname like 'REP_%'";
$res = $xoopsDB->query($sql);
while (list($datnam, $datval) = $xoopsDB->fetchRow($res)) {
    $NewConf[$datnam] = $datval;
}

print '<form name="form1" method="post" action="repadm.php">
<table class="outer">
<thead><tr><th colspan="2">' . _LCX_ADM_REPCONF . '</th></tr></thead>
<tr>
<td>' . _LCX_ADM_ROWLIMIT . '</td>
<td>
' . _LCX_ADM_FOR_GUEST . '<input type="text" size="4" name="REP_ROWLIMIT" value="' . $NewConf['REP_ROWLIMIT'] . '">&nbsp;&nbsp;
' . _LCX_ADM_FOR_USERS . '<input type="text" size="4" name="REP_ROWLIMIT_USR" value="' . $NewConf['REP_ROWLIMIT_USR'] . '">&nbsp;&nbsp;
' . _LCX_ADM_FOR_ADMIN . '<input type="text" size="4" name="REP_ROWLIMIT_ADM" value="' . $NewConf['REP_ROWLIMIT_ADM'] . '">
</td>
</tr>
';

foreach ($fld as $f) {
    $n = $nam[$f];
    print '<tr><td>' . $n . '</td><td>';
    for ($i = 0; $i <= 3; ++$i) {
        if ($NewConf[$f] == $i) {
            print '<input type="radio" name="' . $f . '" value="' . $i . '" style="background-color:#00FF00;" checked="checked">' . $opt[$i];
        } else {
            print '<input type="radio" name="' . $f . '" value="' . $i . '">' . $opt[$i];
        }
    }
    print "</td></tr>";
}

print '<tfoot>
<tr>
<td><input type="hidden" name="command" value="edit"></td>
<td><input type="submit" value="' . _SUBMIT . '" name="save"></td>
</tr></tfoot>
</table></form>
';

include_once "./admin_footer.php";
