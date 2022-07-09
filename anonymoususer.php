<?php

require_once 'anonymoususer.civix.php';

// phpcs:disable
use CRM_Anonymoususer_ExtensionUtil as E;

// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function anonymoususer_civicrm_config(&$config)
{
    _anonymoususer_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function anonymoususer_civicrm_xmlMenu(&$files)
{
    _anonymoususer_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function anonymoususer_civicrm_install()
{
    _anonymoususer_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function anonymoususer_civicrm_postInstall()
{
    _anonymoususer_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function anonymoususer_civicrm_uninstall()
{
    _anonymoususer_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function anonymoususer_civicrm_enable()
{
    _anonymoususer_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function anonymoususer_civicrm_disable()
{
    _anonymoususer_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function anonymoususer_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL)
{
    return _anonymoususer_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function anonymoususer_civicrm_managed(&$entities)
{
    _anonymoususer_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Add CiviCase types provided by this extension.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function anonymoususer_civicrm_caseTypes(&$caseTypes)
{
    _anonymoususer_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Add Angular modules provided by this extension.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function anonymoususer_civicrm_angularModules(&$angularModules)
{
    // Auto-add module files from ./ang/*.ang.php
    _anonymoususer_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function anonymoususer_civicrm_alterSettingsFolders(&$metaDataFolders = NULL)
{
    _anonymoususer_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function anonymoususer_civicrm_entityTypes(&$entityTypes)
{
    _anonymoususer_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_themes().
 */
function anonymoususer_civicrm_themes(&$themes)
{
    _anonymoususer_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function anonymoususer_civicrm_preProcess($formName, &$form) {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function anonymoususer_civicrm_navigationMenu(&$menu) {
//  _anonymoususer_civix_insert_navigation_menu($menu, 'Mailings', [
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ]);
//  _anonymoususer_civix_navigationMenu($menu);
//}

function anonymoususer_civicrm_pre($op, $objectName, $objectId, &$params)
{
    // If this is contribution and operation is create and params['cid'] or ['contact_id'] is empty.
    $external_id = CRM_Anonymoususer_Upgrader::EXTERNAL_ID;
    $email = CRM_Anonymoususer_Upgrader::EMAIL;
    $first_name = CRM_Anonymoususer_Upgrader::FIRST_NAME;
    $last_name = CRM_Anonymoususer_Upgrader::LAST_NAME;
    //
//    CRM_Core_Error::debug_var('op', $op);
//    CRM_Core_Error::debug_var('objectName', $objectName);
//    CRM_Core_Error::debug_var('params', $params);
    if ($op === 'create' && ($objectName === 'Profile')) {
        $email_primary = strval($params['email-Primary']);
        $email_five = strval($params['email-five']);
        if ($email_primary === null || $email_primary === "" || $email_primary === FALSE) {
            $params['email-Primary'] = $email;
        }
        if ($params['email-Primary'] == $email) {
            if (!isset($params['contact_id']) || $params['contact_id'] == null) {
                $result_old = civicrm_api3('Contact', 'get', ['sequential' => 1,
                    'external_identifier' => $external_id,
                ]);
//        CRM_Core_Error::debug_var('find_user', $result);
                if ($result_old['count'] > 0) {
                    $anonymous = $result_old['values'];
                    $anonymous = reset($anonymous);
                    $anonymous_id = $anonymous['id'];
                    $params['contact_id'] = $anonymous_id;
                    $params['first_name'] = $first_name;
                    $params['last_name'] = $last_name;
                }
            }
        }
    }
}
