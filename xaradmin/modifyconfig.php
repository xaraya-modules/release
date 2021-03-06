<?php
/**
 * Standard function to modify configuration parameters
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
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function release_admin_modifyconfig()
{
    // Security check
    if (!xarSecurity::check('AdminRelease')) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
        return;
    }
    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'release']);
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_short_urls', 'use_module_icons, frontend_page, backend_page');
    $data['module_settings']->getItem();

    switch (strtolower($phase)) {
        case 'modify':
        default:

            switch ($data['tab']) {
                case 'general':
                    break;
                default:
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code
            if (!xarSec::confirmAuthKey()) {
                return;
            }
            switch ($data['tab']) {
                case 'general':
                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        return xarTpl::module('release', 'admin', 'modifyconfig', $data);
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }
                    break;
                default:
                    break;
            }
            // Jump to the next page
            xarController::redirect(xarController::URL('release', 'admin', 'modifyconfig', ['tab' => $data['tab']]));
            return true;
            break;
    }

    $hooks = xarModHooks::call(
        'module',
        'modifyconfig',
        'release',
        ['module' => 'release']
    );
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    // Return the template variables defined in this function
    return $data;
}
