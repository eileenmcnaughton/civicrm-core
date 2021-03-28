<?php
// This file declares an Angular module which can be autoloaded
// in CiviCRM. See also:
// \https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules/n
return [
  'js' => [
    'ang/paymentblock.js',
    'ang/paymentblock/*.js',
    'ang/paymentblock/*/*.js',
  ],
  'css' => [
    'ang/paymentblock.css',
  ],
  'partials' => [
    'ang/paymentblock',
  ],
  'requires' => [
    'crmUi',
    'crmUtil',
    'ngRoute',
  ],
  'settings' => [],
];
