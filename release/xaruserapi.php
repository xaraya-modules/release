<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: John Cox via phpMailer Team
// Purpose of file: standard mail output
// ----------------------------------------------------------------------

/**
 * get all users
 * @returns array
 * @return array of users, or false on failure
 */
function release_userapi_getallids($args)
{
    extract($args);

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $releaseinfo = array();

    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_OVERVIEW)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    $query = "SELECT xar_rid,
                     xar_uid,
                     xar_name,
                     xar_desc,
                     xar_type,
                     xar_approved
            FROM $releasetable
            ORDER BY xar_rid";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rid, $uid, $name, $desc, $type, $approved) = $result->fields;
        if (xarSecAuthAction(0, 'release::', "::", ACCESS_OVERVIEW)) {
            $releaseinfo[] = array('rid'        => $rid,
                                   'uid'        => $uid,
                                   'name'       => $name,
                                   'desc'       => $desc,
                                   'type'       => $type,
                                   'approved'   => $approved);
        }
    }

    $result->Close();

    // Return the users
    return $releaseinfo;
}

function release_userapi_get($args)
{
    extract($args);

    if (!isset($rid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Get link
    $query = "SELECT xar_rid,
                     xar_uid,
                     xar_name,
                     xar_desc,
                     xar_type,
                     xar_approved
            FROM $releasetable
            WHERE xar_rid = " . xarVarPrepForStore($rid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($rid, $uid, $name, $desc, $type, $approved) = $result->fields;
    $result->Close();

    if (!xarSecAuthAction(0, 'Release::', "$name::$rid", ACCESS_READ)) {
        return false;
    }

    $releaseinfo = array('rid'        => $rid,
                         'uid'        => $uid,
                         'name'       => $name,
                         'desc'       => $desc,
                         'type'       => $type,
                         'approved'   => $approved);

    return $releaseinfo;
}

function release_userapi_createid($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if ((!isset($name)) ||
        (!isset($type))) {

        $msg = xarML('Wrong arguments to release_userapi_create.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Check if that username exists
    $query = "SELECT xar_rid FROM $releasetable
            WHERE xar_name='".xarVarPrepForStore($name)."';";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if ($result->RecordCount() > 0) {
        return; 
    }

    if (empty($approved)){
        $approved = 1;
    }

    $query = "INSERT INTO $releasetable (
              xar_rid,
              xar_uid,
              xar_name,
              xar_desc,
              xar_type,
              xar_approved
              )
            VALUES (
              '" . xarVarPrepForStore($rid) . "',
              '" . xarVarPrepForStore($uid) . "',
              '" . xarVarPrepForStore($name) . "',
              '" . xarVarPrepForStore($desc) . "',
              '" . xarVarPrepForStore($type) . "',
              '" . xarVarPrepForStore($approved) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'create', $rid, 'rid');

    // Return the id of the newly created user to the calling process
    return $rid;

}

function release_userapi_updateid($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($rid)) ||
        (!isset($uid)) ||
        (!isset($name)) ||
        (!isset($type))) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'update', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('release',
                          'user',
                          'get',
                          array('rid' => $rid));

    if ($link == false) {
        $msg = xarML('No Such Release ID Present',
                    'release');
        xarExceptionSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    if (!xarSecAuthAction(0, 'Release::', "::", ACCESS_READ)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Update the link
    $query = "UPDATE $releasetable
            SET xar_uid = '" . xarVarPrepForStore($uid) . "',
                xar_name = '" . xarVarPrepForStore($name) . "',
                xar_type = '" . xarVarPrepForStore($type) . "',
                xar_desc = '" . xarVarPrepForStore($desc) . "',
                xar_approved = '" . xarVarPrepForStore($approved) . "'
            WHERE xar_rid = " . xarVarPrepForStore($rid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'update', $rid, 'rid');

    // Return the id of the newly created user to the calling process
    return $rid;
}

function release_userapi_getmenulinks()
{
    if (xarSecAuthAction(0, 'users::', '::', ACCESS_OVERVIEW)) {
        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'viewids'),
                             'title' => xarML('View all theme and module IDs'),
                             'label' => xarML('View IDs'));


        $menulinks[] = Array('url'   => xarModURL('release',
                                                  'user',
                                                  'addid'),
                             'title' => xarML('Add a module or theme ID so it will not be duplicated'),
                             'label' => xarML('Add ID'));

    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>