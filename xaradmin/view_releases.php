<?php
/*
 * View all Releases
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_admin_view_releases()
{
    if (!xarSecurityCheck('EditRelease')) return;

    // Get the object to be listed
    $data['object'] = DataObjectMaster::getObjectList(array('name' => 'release_notes'));

    return $data;
}

?>