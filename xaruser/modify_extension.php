<?php
/**
 * Modify an extension
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Modify an extension by user
 *
 * @param $rid
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_user_modify_extension($args)
{
    if (!xarSecurity::check('EditRelease')) {
        return;
    }

    if (!xarVar::fetch('name', 'str', $name, 'release_extensions', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['object']->getItem(['itemid' => $data['itemid']]);

    $data['tplmodule'] = 'release';

    if ($data['confirm']) {
        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Get the data from the form
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTpl::module('release', 'user', 'modify_extension', $data);
        } else {
            // Good data: create the item
            $itemid = $data['object']->updateItem(['itemid' => $data['itemid']]);

            // Jump to the next page
            xarController::redirect(xarController::URL('release', 'user', 'view_extensions'));
            return true;
        }
    }
    return $data;
}
