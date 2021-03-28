<?php


namespace Civi\Api4\PaymentForm\Submit;

use Civi;
use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Token\TokenProcessor;

/**
 * Class Parse.
 *
 * Parse a name into component parts.
 *
 * @method $this setNames(array $names) Set names to parse.
 * @method array getNames() Get names to parse.
 */
class Submit extends AbstractAction {

  /**
   * Names to parse.
   *
   * @var array
   */
  protected $names = [];

  /**
   * @inheritDoc
   *
   * @param \Civi\Api4\Generic\Result $result
   *
   * @throws \API_Exception
   */
  public function _run(Result $result) {

  }

}
