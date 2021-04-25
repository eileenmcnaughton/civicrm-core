<?php
namespace Civi\Token;

use Civi\Api4\Contact;
use Civi\Token\Event\TokenRenderEvent;
use Civi\Token\Event\TokenValueEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class TokenCompatSubscriber
 * @package Civi\Token
 *
 * This class provides a compatibility layer for using CRM_Utils_Token
 * helpers within TokenProcessor.
 *
 * THIS IS NOT A GOOD EXAMPLE TO EMULATE. The class exists to two
 * bridge two different designs. CRM_Utils_Token has some
 * undesirable elements (like iterative token substitution).
 * However, if you're refactor CRM_Utils_Token or improve the
 * bridge, then it makes sense to update this class.
 */
class TokenCompatSubscriber implements EventSubscriberInterface {

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    return [
      'civi.token.eval' => 'onEvaluate',
      'civi.token.render' => 'onRender',
    ];
  }

  /**
   * Load token data.
   *
   * @param \Civi\Token\Event\TokenValueEvent $e
   * @throws TokenException
   */
  public function onEvaluate(TokenValueEvent $e) {
    // For reasons unknown, replaceHookTokens used to require a pre-computed list of
    // hook *categories* (aka entities aka namespaces). We cache
    // this in the TokenProcessor's context but can likely remove it now.

    $e->getTokenProcessor()->context['hookTokenCategories'] = \CRM_Utils_Token::getTokenCategories();

    $messageTokens = $e->getTokenProcessor()->getMessageTokens();
    $returnProperties = array_fill_keys($messageTokens['contact'] ?? [], 1);
    $returnProperties = array_merge(\CRM_Contact_BAO_Query::defaultReturnProperties(), $returnProperties);

    foreach ($e->getRows() as $row) {
      if (empty($row->context['contactId'])) {
        continue;
      }
      /** @var int $contactId */
      $contactId = $row->context['contactId'];
      if (empty($row->context['contact'])) {
        $params = [
          ['contact_id', '=', $contactId, 0, 0],
        ];
        $contactResult = (array) Contact::get(FALSE)
          ->setSelect(array_keys($returnProperties))->addWhere('id', '=', $contactId)
          ->execute()->first();
        [$contact] = \CRM_Contact_BAO_Query::apiQuery($params, $returnProperties ?? NULL);
        //CRM-4524
        $contact = reset($contact);
        $diff = array_diff_key($contact, $contactResult);
        // Test cover for greeting in CRM_Core_BAO_ActionScheduleTest::testMailer
        $contact['email_greeting'] = $contact['email_greeting_display'] ?? '';
        $contact['postal_greeting'] = $contact['postal_greeting_display'] ?? '';
        $contact['addressee'] = $contact['address_display'] ?? '';
        if (!$contact || is_a($contact, 'CRM_Core_Error')) {
          // FIXME: Need to differentiate errors which kill the batch vs the individual row.
          \Civi::log()->debug('Failed to generate token data. Invalid contact ID: ' . $row->context['contactId']);
          continue;
        }

        //update value of custom field token
        if (!empty($messageTokens['contact'])) {
          foreach ($messageTokens['contact'] as $token) {
            if (\CRM_Core_BAO_CustomField::getKeyID($token)) {
              $contact[$token] = \CRM_Core_BAO_CustomField::displayValue($contact[$token], \CRM_Core_BAO_CustomField::getKeyID($token));
            }
          }
        }
      }
      else {
        $contact = $row->context['contact'];
      }

      if (!empty($row->context['tmpTokenParams'])) {
        // merge activity tokens with contact array
        // this is pretty weird.
        $contact = array_merge($contact, $row->context['tmpTokenParams']);
      }

      $contactArray = [$contactId => $contact];
      \CRM_Utils_Hook::tokenValues($contactArray,
        [$contactId],
        empty($row->context['mailingJobId']) ? NULL : $row->context['mailingJobId'],
        $messageTokens,
        $row->context['controller']
      );

      // merge the custom tokens in the $contact array
      if (!empty($contactArray[$contactId])) {
        $contact = array_merge($contact, $contactArray[$contactId]);
      }
      $row->context('contact', $contact);
    }
  }

  public function getExpectedFields() {
    return [
      'image_URL',
      'legal_identifier',
      'external_identifier',
      'contact_type',
      'contact_sub_type',
      'sort_name',
      'display_name',
      'preferred_mail_format' => 1,
      'nick_name' => 1,
      'first_name' => 1,
      'middle_name' => 1,
      'last_name' => 1,
      'prefix_id' => 1,
      'suffix_id' => 1,
      'formal_title' => 1,
      'communication_style_id' => 1,
      'birth_date' => 1,
      'gender_id' => 1,
      'street_address' => 1,
      'supplemental_address_1' => 1,
      'supplemental_address_2' => 1,
      'supplemental_address_3' => 1,
      'city' => 1,
      'postal_code' => 1,
      'postal_code_suffix' => 1,
      'state_province' => 1,
      'country' => 1,
      'world_region' => 1,
      'geo_code_1' => 1,
      'geo_code_2' => 1,
      'email' => 1,
      'on_hold' => 1,
      'phone' => 1,
      'im' => 1,
      'household_name' => 1,
      'organization_name' => 1,
      'deceased_date' => 1,
      'is_deceased' => 1,
      'job_title' => 1,
      'legal_name' => 1,
      'sic_code' => 1,
      'current_employer' => 1,
      'do_not_email' => 1,
      'do_not_mail' => 1,
      'do_not_sms' => 1,
      'do_not_phone' => 1,
      'do_not_trade' => 1,
      'is_opt_out' => 1,
      'contact_is_deleted' => 1,
      'preferred_communication_method' => 1,
      'preferred_language' => 1,
      'address_id' => 1,
      'address_name' => 1,
      'addressee' => 1,
      'checksum' => 1,
      'communication_style' => 1,
      'contact_id' => 1,
      'source',
      'county',
      'created_date',
      'current_employer_id',
      'custom_1' => 1,
      'custom_10' => 1,
      'custom_11' => 1,
      'custom_12' => 1,
      'custom_13' => 1,
      'custom_2' => 1,
      'custom_3' => 1,
      'custom_4' => 1,
      'custom_5' => 1,
      'custom_7' => 1,
      'custom_8' => 1,
      'custom_9' => 1,
      'email_greeting',
      'postal_greeting',
      'gender' => 1,
      'hash' => 1,
      'im_provider' => 1,
      'individual_prefix' => 1,
      'individual_suffix' => 1,
      'location_type' => 1,
      'manual_geo_code' => 1,
      'master_id' => 1,
      'modified_date' => 1,
      'openid' => 1,
      'phone_ext' => 1,
      'phone_type' => 1,
      'phone_type_id' => 1,
      // email
      'signature_html' => 1,
      'signature_text' => 1,
      // Address
      'street_name' => 1,
      'street_number' => 1,
      'street_number_suffix' => 1,
      'street_unit' => 1,
      // website
      'home_URL',
      'url',
    ];
  }
  /**
   * Apply the various CRM_Utils_Token helpers.
   *
   * @param \Civi\Token\Event\TokenRenderEvent $e
   */
  public function onRender(TokenRenderEvent $e) {
    $isHtml = ($e->message['format'] == 'text/html');
    $useSmarty = !empty($e->context['smarty']);

    $domain = \CRM_Core_BAO_Domain::getDomain();
    $e->string = \CRM_Utils_Token::replaceDomainTokens($e->string, $domain, $isHtml, $e->message['tokens'], $useSmarty);

    if (!empty($e->context['contact'])) {
      \CRM_Utils_Token::replaceGreetingTokens($e->string, $e->context['contact'], $e->context['contact']['contact_id'], NULL, $useSmarty);
      $e->string = \CRM_Utils_Token::replaceContactTokens($e->string, $e->context['contact'], $isHtml, $e->message['tokens'], TRUE, $useSmarty);

      // FIXME: This may depend on $contact being merged with hook values.
      $e->string = \CRM_Utils_Token::replaceHookTokens($e->string, $e->context['contact'], $e->context['hookTokenCategories'], $isHtml, $useSmarty);
    }

    if ($useSmarty) {
      $smarty = \CRM_Core_Smarty::singleton();
      $e->string = $smarty->fetch("string:" . $e->string);
    }
  }

}
