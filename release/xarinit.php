<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: John Cox via phpMailer Team 
// Purpose of file:  Initialisation functions for the Mail Hook
// ----------------------------------------------------------------------

//Load Table Maintainance API
xarDBLoadTableMaintenanceAPI();
/**
 * initialise the send to friend module
 */
function release_init()
{
    // Set up database tables
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // *_user_data
    $query = xarDBCreateTable($releasetable,
                             array('xar_rid'         => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => true,
                                                              'primary_key' => true),
                                   'xar_name'        => array('type'        => 'varchar',
                                                              'size'        => 100,
                                                              'null'        => false,
                                                              'default'     => ''),
                                   'xar_desc'        => array('type'        => 'text',
                                                              'default'     => ''),
                                   'xar_type'        => array('type'        => 'varchar',
                                                              'size'        => 100,
                                                              'null'        => false,
                                                              'default'     => ''),
                                   'xar_approved'    => array('type'        => 'integer',
                                                              'null'        => false,
                                                              'default'     => '0',
                                                              'increment'   => false)));
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

/**
 * upgrade the send to friend module from an old version
 */
function release_upgrade($oldversion)
{
    return false;
}

/**
 * delete the send to friend module
 */
function release_delete()
{

    // Set up database tables
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Drop the table
    $query = "DROP TABLE $xartable[release_id]";

    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    return true;
}

?>