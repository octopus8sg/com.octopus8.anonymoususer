<?php

use CRM_Anonymoususer_ExtensionUtil as E;

/**
 * Class CRM_Anonymoususer_Upgrader
 */
class CRM_Anonymoususer_Upgrader extends CRM_Anonymoususer_Upgrader_Base
{
    public const EXTERNAL_ID = "ANONYMOUS_USER";
    public const EMAIL = "anonymous@user.contact";
    public const PROFILE_NAME = "anonymous_profile";
    public const FIELD_NAME = "Email (Optional)";
    public const FIRST_NAME = "ANONYMOUS";
    public const LAST_NAME = "USER";
    // By convention, functions that look like "function upgrade_NNNN()" are
    // upgrade tasks. They are executed in order (like Drupal's hook_update_N).
    private static function create_anonymous_user(): void
    {
        $first_name =  self::FIRST_NAME;
        $last_name = self::LAST_NAME;
        $external_id = self::EXTERNAL_ID;
        $email = self::EMAIL;
        $result_old = civicrm_api3('Contact', 'get', ['sequential' => 1,
            'external_identifier' => $external_id,
        ]);
//        CRM_Core_Error::debug_var('find_user', $result);
        $contactArray = ['sequential' => 1,
            'contact_type' => 'Individual',
            'first_name' => $first_name,
            'last_name' => $last_name,
            'external_identifier' => $external_id,
            'do_not_phone' => TRUE,
            'do_not_email' => TRUE,
            'do_not_mail' => TRUE,
            'do_not_sms' => TRUE,
            'do_not_trade' => TRUE,
            'is_deleted' => FALSE,
            'email' => $email,
        ];
        if ($result_old['count'] == 0) {
            // Create the contact.
            $result_new = civicrm_api3('Contact', 'create', $contactArray);
        } else {
            $anonymous = $result_old['values'];
            $anonymous = reset($anonymous);
            $anonymous_id = $anonymous['id'];
            $contactArray['id'] = $anonymous_id;
            $result_new = civicrm_api3('Contact', 'create', $contactArray);
        }

//                CRM_Core_Error::debug_var('create_user', $result);
    }

    private static function create_anonymous_profile()
    {
        $search_params_profile = [
            'sequential' => 1,
            'name' => self::PROFILE_NAME
        ];
        $result_profile = civicrm_api3('UFGroup', 'get', $search_params_profile);
//        CRM_Core_Error::debug_var('find_user', $result);
        $profile_id = null;
        $profile_params = [
            'title' => 'Anonymous Payments Profile',
            'frontend_title' => 'Anonymous Payments Profile',
            'description' => 'Anonymous Payments Profile',
            'name' => self::PROFILE_NAME,
            'weight' => 1,
            'is_active' => 1,
            'is_update_dupe' => 0,
            'is_cms_user' => 0,
            'is_proximity_search' => 0,
            'is_reserved' => 1,
            'group_type' => 'Contact',
        ];

        if ($result_profile['count'] == 0) {
            // Create the contact.
            $result_new = civicrm_api3('UFGroup', 'create', $profile_params);
            $profile_id = $result_new['id'];
        } else {
            $anonymous = $result_profile['values'];
            $anonymous = reset($anonymous);
            $profile_id = $anonymous['id'];
            $profile_params['id'] = $profile_id;
            $result_new = civicrm_api3('UFGroup', 'create', $profile_params);
            $profile_id = $result_new['id'];
        }
        return $profile_id;
//                CRM_Core_Error::debug_var('create_user', $result);
    }

    private static function create_anonymous_email_field($group_id): void
    {
//        $group_id = null;
        //first search for profile
        $field_id = null;
        $field_label = self::FIELD_NAME;
        $field_name = "email";
        $result_field = civicrm_api3('UFField', 'get', ['sequential' => 1,
            'field_name' => $field_name,
            'field_label' => $field_label,
            'group_id' => $group_id
        ]);
//        CRM_Core_Error::debug_var('find_user', $result);
        $field_params = ['group_id' => $group_id,
            'field_id' => $field_id,
            'field_name' => $field_name,
            'visibility' => 'User and User Admin Only',
            'in_selector' => 0,
            'is_searchable' => 0,
            'weight' => 1,
            'help_pre' => "",
            'help_post' => "",
            'is_required' => FALSE,
            'is_multi_summary' => FALSE,
            'is_active' => 1,
            'is_view' => 1,
            'label' => $field_label,
            'uf_group_id' => $group_id,
            'id' => $field_id,
            'field_type' => 'Contact',
            'location_type_id' => null,
            'version' => 3];
        if ($result_field['count'] == 0) {
            // Create the contact.
            $result_new = civicrm_api3('UFField', 'create', $field_params);
        } else {
            $anonymous = $result_field['values'];
            $anonymous = reset($anonymous);
            $anonymous_id = $anonymous['id'];
            $field_params['id'] = $anonymous_id;
            $result_new = civicrm_api3('UFField', 'create', $field_params);
        }

//                CRM_Core_Error::debug_var('create_user', $result);
    }

    /**
     * Example: Run an external SQL script when the module is installed.
     *
     */
    public function install()
    {
        self::create_anonymous_user();
        $group_id = self::create_anonymous_profile();
        self::create_anonymous_email_field($group_id);
    }

    /**
     * Example: Work with entities usually not available during the install step.
     *
     * This method can be used for any post-install tasks. For example, if a step
     * of your installation depends on accessing an entity that is itself
     * created during the installation (e.g., a setting or a managed entity), do
     * so here to avoid order of operation problems.
     */
    // public function postInstall() {
    //  $customFieldId = civicrm_api3('CustomField', 'getvalue', array(
    //    'return' => array("id"),
    //    'name' => "customFieldCreatedViaManagedHook",
    //  ));
    //  civicrm_api3('Setting', 'create', array(
    //    'myWeirdFieldSetting' => array('id' => $customFieldId, 'weirdness' => 1),
    //  ));
    // }

    /**
     * Example: Run an external SQL script when the module is uninstalled.
     */
    // public function uninstall() {
    //  $this->executeSqlFile('sql/myuninstall.sql');
    // }

    /**
     * Example: Run a simple query when a module is enabled.
     */
    public function enable()
    {
        self::create_anonymous_user();
        $group_id = self::create_anonymous_profile();
        self::create_anonymous_email_field($group_id);

    }

    /**
     * Example: Run a simple query when a module is disabled.
     */
    // public function disable() {
    //   CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 0 WHERE bar = "whiz"');
    // }

    /**
     * Example: Run a couple simple queries.
     *
     * @return TRUE on success
     * @throws Exception
     */
    // public function upgrade_4200(): bool {
    //   $this->ctx->log->info('Applying update 4200');
    //   CRM_Core_DAO::executeQuery('UPDATE foo SET bar = "whiz"');
    //   CRM_Core_DAO::executeQuery('DELETE FROM bang WHERE willy = wonka(2)');
    //   return TRUE;
    // }


    /**
     * Example: Run an external SQL script.
     *
     * @return TRUE on success
     * @throws Exception
     */
    // public function upgrade_4201(): bool {
    //   $this->ctx->log->info('Applying update 4201');
    //   // this path is relative to the extension base dir
    //   $this->executeSqlFile('sql/upgrade_4201.sql');
    //   return TRUE;
    // }


    /**
     * Example: Run a slow upgrade process by breaking it up into smaller chunk.
     *
     * @return TRUE on success
     * @throws Exception
     */
    // public function upgrade_4202(): bool {
    //   $this->ctx->log->info('Planning update 4202'); // PEAR Log interface

    //   $this->addTask(E::ts('Process first step'), 'processPart1', $arg1, $arg2);
    //   $this->addTask(E::ts('Process second step'), 'processPart2', $arg3, $arg4);
    //   $this->addTask(E::ts('Process second step'), 'processPart3', $arg5);
    //   return TRUE;
    // }
    // public function processPart1($arg1, $arg2) { sleep(10); return TRUE; }
    // public function processPart2($arg3, $arg4) { sleep(10); return TRUE; }
    // public function processPart3($arg5) { sleep(10); return TRUE; }

    /**
     * Example: Run an upgrade with a query that touches many (potentially
     * millions) of records by breaking it up into smaller chunks.
     *
     * @return TRUE on success
     * @throws Exception
     */
    // public function upgrade_4203(): bool {
    //   $this->ctx->log->info('Planning update 4203'); // PEAR Log interface

    //   $minId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(min(id),0) FROM civicrm_contribution');
    //   $maxId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(max(id),0) FROM civicrm_contribution');
    //   for ($startId = $minId; $startId <= $maxId; $startId += self::BATCH_SIZE) {
    //     $endId = $startId + self::BATCH_SIZE - 1;
    //     $title = E::ts('Upgrade Batch (%1 => %2)', array(
    //       1 => $startId,
    //       2 => $endId,
    //     ));
    //     $sql = '
    //       UPDATE civicrm_contribution SET foobar = whiz(wonky()+wanker)
    //       WHERE id BETWEEN %1 and %2
    //     ';
    //     $params = array(
    //       1 => array($startId, 'Integer'),
    //       2 => array($endId, 'Integer'),
    //     );
    //     $this->addTask($title, 'executeSql', $sql, $params);
    //   }
    //   return TRUE;
    // }

}
