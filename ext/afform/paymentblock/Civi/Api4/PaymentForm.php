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

namespace Civi\Api4;

use Civi\Api4\Generic\BasicGetFieldsAction;

/**
 * MsgTemplate entity.
 *
 * This is a collection of MsgTemplate, for reuse in import, export, etc.
 *
 * @package Civi\Api4
 */
class PaymentForm extends Generic\AbstractEntity {

  /**
   * @return \Civi\Api4\Action\PaymentForm\Submit
   *
   */
  public static function submit(): Action\PaymentForm\Submit {
    return new Action\PaymentForm\Submit(__CLASS__, __FUNCTION__);
  }

  /**
   * Get permissions.
   *
   * It may be that we don't need a permission check on this api at all at there is a check on the entity
   * retrieved.
   *
   * @return array
   */
  public static function permissions():array {
    return ['parse' => \CRM_Core_Permission::ALWAYS_ALLOW_PERMISSION];
  }

  /**
   * @param bool $checkPermissions
   * @return Generic\BasicGetFieldsAction
   */
  public static function getFields($checkPermissions = TRUE) {
    return (new Generic\BasicGetFieldsAction('PaymentForm', __FUNCTION__, function() {
      return [
        [
          'name' => 'name',
          'description' => 'Entity name',
        ],
        [
          'name' => 'title',
          'description' => 'Localized title (singular)',
        ],
        [
          'name' => 'title_plural',
          'description' => 'Localized title (plural)',
        ],
        [
          'name' => 'type',
          'data_type' => 'Array',
          'description' => 'Base class for this entity',
          'options' => [
            'AbstractEntity' => 'AbstractEntity',
            'DAOEntity' => 'DAOEntity',
            'BasicEntity' => 'BasicEntity',
            'EntityBridge' => 'EntityBridge',
            'OptionList' => 'OptionList',
          ],
        ],
        [
          'name' => 'description',
          'description' => 'Description from docblock',
        ],
        [
          'name' => 'comment',
          'description' => 'Comments from docblock',
        ],
        [
          'name' => 'icon',
          'description' => 'crm-i icon class associated with this entity',
        ],
        [
          'name' => 'dao',
          'description' => 'Class name for dao-based entities',
        ],
        [
          'name' => 'label_field',
          'description' => 'Field to show when displaying a record',
        ],
        [
          'name' => 'searchable',
          'description' => 'Should this entity be selectable in search kit UI',
        ],
        [
          'name' => 'paths',
          'data_type' => 'Array',
          'description' => 'System paths for accessing this entity',
        ],
        [
          'name' => 'see',
          'data_type' => 'Array',
          'description' => 'Any @see annotations from docblock',
        ],
        [
          'name' => 'bridge',
          'data_type' => 'Array',
          'description' => 'Connecting fields for EntityBridge types',
        ],
        [
          'name' => 'ui_join_filters',
          'data_type' => 'Array',
          'description' => 'When joining entities in the UI, which fields should be presented by default in the ON clause',
        ],
      ];
    }))->setCheckPermissions($checkPermissions);
  }
}
