<?php

namespace Civi\Import\Parser;

use CRM_Civiimport_ExtensionUtil as E;

/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */

/**
 * class to parse contact csv files
 */
class Generic extends \CRM_Import_Parser {

  /**
   * Get information about the provided job.
   *
   *  - name
   *  - id (generally the same as name)
   *  - label
   *
   * @return array
   */
  public static function getUserJobInfo(): array {
    $entities = \CRM_Core_DAO_AllCoreTables::getEntities();
    $jobInfo = [];
    foreach ($entities as $entity) {
      if (!empty($entity['importable'])) {
        $entityName = \CRM_Utils_String::munge($entity['name']);
        $name = $entityName . '_import';
        $jobInfo[$name ] = [
          'id' => $name,
          'name' => $name,
          'entity' => $entity['name'],
          'label' => E::ts('%1 Import', [1 => $entity['name']]),
          'url' => 'civicrm/import/' . $entityName,
        ];
      }

    }
    return $jobInfo;
  }


  /**
   * Set field metadata.
   */
  protected function setFieldMetadata(): void {
    if (empty($this->importableFieldsMetadata)) {
      $fields = ['' => ['title' => ts('- do not import -')]];
      $apiv4 = Contribution::getFields(TRUE)->addWhere('custom_field_id', '>', 0)->execute();
      $customFields = [];
      foreach ($apiv4 as $apiv4Field) {
        $customFields[$apiv4Field['name']] = $apiv4Field;
      }
      $fields = array_merge($fields, $customFields);

      $fields['soft_credit.contact.id'] = [
        'title' => ts('Soft Credit Contact ID'),
        'softCredit' => TRUE,
        'name' => 'id',
        'entity' => 'Contact',
        'entity_instance' => 'SoftCreditContact',
        'entity_prefix' => 'soft_credit.contact.',
        'options' => FALSE,
        'type' => CRM_Utils_Type::T_STRING,
        'contact_type' => ['Individual' => 'Individual', 'Household' => 'Household', 'Organization' => 'Organization'],
        'match_rule' => '*',
      ];
      foreach ($tmpContactField as $contactField) {
        $fields['soft_credit.contact.' . $contactField['name']] = array_merge($contactField, [
          'title' => ts('Soft Credit Contact') . ' ' . $contactField['title'],
          'softCredit' => TRUE,
          'entity' => 'Contact',
          'entity_instance' => 'SoftCreditContact',
          'entity_prefix' => 'soft_credit.contact.',
        ]);
      }

      // add pledge fields only if its is enabled
      if (CRM_Core_Permission::access('CiviPledge')) {
        $pledgeFields = [
          'pledge_id' => [
            'title' => ts('Pledge ID'),
            'headerPattern' => '/Pledge ID/i',
            'name' => 'pledge_id',
            // This is handled as a contribution field & the goal is
            // to make it pseudofield on the contribution.
            'entity' => 'Contribution',
            'type' => CRM_Utils_Type::T_INT,
            'options' => FALSE,
          ],
        ];

        $fields = array_merge($fields, $pledgeFields);
      }
      $this->importableFieldsMetadata = $fields;
    }
  }


}
