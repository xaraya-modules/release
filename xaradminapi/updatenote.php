<?php

function release_adminapi_updatenote($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if  (!isset($rnid)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('release',
                          'user',
                          'getnote',
                          array('rnid' => $rnid));

    if ($link == false) {
        $msg = xarML('No Such Release Note Present');
        xarErrorSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;
    $enotes = !empty($enotes)? $enotes :'';
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasenotetable = $xartable['release_notes'];

    // Update the link
    $query = "UPDATE $releasenotetable
            SET xar_version = ?,
                xar_price = ?,
                xar_supported = ?,
                xar_demo = ?,
                xar_dllink = ?,
                xar_demolink = ?,
                xar_priceterms = ?,
                xar_supportlink = ?,
                xar_changelog = ?,
                xar_notes = ?,
                xar_enotes = ?,
                xar_certified = ?,
                xar_approved = ?,
                xar_rstate = ?
            WHERE xar_rnid = ?";
    $bindvars=array($version,$price,$supported,$demo,$dllink,$demolink,$priceterms,$supportlink,
                    $changelog,$notes,$enotes,$certified,$approved,$rstate,$rnid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    // Let any hooks know that we have created a new user.
    $args['module'] = 'release';
    $args['itemtype'] = 0;
    $args['itemid'] = $rnid;
    xarModCallHooks('item', 'update', $rnid, $args);

    // Return the id of the newly created user to the calling process
    return $rnid;
}

?>