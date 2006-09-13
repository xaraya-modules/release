<?php
/**
 * Get module IDs
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */

/**
 * Get module IDs
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 * @TODO 
 */
function release_userapi_updateid($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($eid)) ||
        (!isset($uid)) ||
        (!isset($regname)) ||
        (!isset($displname)) ||
        (!isset($exttype)) ||
        (!isset($class))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('release', 'user', 'getid',
                          array('eid' => $eid));

    if ($link == false) {
        $msg = xarML('No Such Release ID Present');
        xarErrorSet(XAR_USER_EXCEPTION,
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return;
    }
    //this should not change once registered
    if (!isset($regtime)) {
       $regtime=$link['regtime'];
    }
    $modified = time();

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    if (empty($approved)){
        $approved = '1';
    }

    $releasetable = $xartable['release_id'];

    // Update the link
    $query = "UPDATE $releasetable
            SET xar_uid       = ?,
                xar_regname   = ?,
                xar_displname = ?,
                xar_class     = ?,
                xar_desc      = ?,
                xar_certified = ?,
                xar_approved  = ?,
                xar_rstate    = ?,
                xar_regtime   = ?,
                xar_modified  = ?,
                xar_members   = ?,
                xar_scmlink   = ?,
                xar_openproj  = ?,
                xar_exttype   = ?
            WHERE xar_eid     = ?";
    $bindvars = array((int)$uid,$regname,$displname,$class,$desc,$certified,$approved,$rstate,
                      $regtime, $modified, $members, $scmlink, $openproj, (int)$exttype,(int)$eid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    if (empty($cids)) {
        $cids = array();
    }
    $args['module'] = 'release';
    $args['cids'] = $cids;

    // Let the calling process know that we have finished successfully

    xarModCallHooks('item', 'update', $eid, $args);

    // Return the id of the newly created user to the calling process
    return $eid;
}

?>