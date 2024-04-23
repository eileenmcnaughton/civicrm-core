<?php
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
 * This class previews the uploaded file and returns summary statistics.
 */
class CRM_Import_Form_Generic_Parser extends CRM_Import_Parser {

  private string $entity;

  public function getEntity(): string {
    return $this->entity;
  }

  public function __construct($entity) {
    $this->entity = $entity;
  }

  /**
   * Get information about the provided job.
   *  - name
   *  - id (generally the same as name)
   *  - label
   *
   *  e.g. ['activity_import' => ['id' => 'activity_import', 'label' => ts('Activity Import'), 'name' => 'activity_import']]
   *
   * @return array
   */
  public static function getUserJobInfo(): array {
    return [
      'entity_import' => [
        'id' => 'entity_import',
        'name' => 'entity_import',
        'label' => ts('Entity Import'),
        'entity' => 'Entity',
        'url' => 'civicrm/import/entity',
      ],
    ];
  }

}
