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
        $name = strtolower($entityName) . '_import';
        $jobInfo[$name ] = [
          'id' => $name,
          'name' => $name,
          'entity' => $entity['name'],
          'label' => E::ts('%1 Import', [1 => $entity['name']]),
          'url' => 'civicrm/import/' . strtolower($entityName),
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
      $jobType = \CRM_Core_BAO_UserJob::getType($this->jobType);
      $fields = ['' => ['title' => E::ts('- do not import -')]]
        + $this->getImportFieldsForEntity($jobType['entity']);
      $this->importableFieldsMetadata = $fields;
    }
  }


}
