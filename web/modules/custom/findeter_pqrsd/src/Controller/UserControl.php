<?php

namespace Drupal\findeter_pqrsd\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
* Builds an example page.
*/
class UserControl {

  /**
  * Checks access for a specific request.
  *
  * @param \Drupal\Core\Session\AccountInterface $account
  *   Run access checks for this account.
  *
  * @return \Drupal\Core\Access\AccessResultInterface
  *   The access result.
  */
  public function access(AccountInterface $account) {
  // Check permissions and combine that with any custom access checking needed. Pass forward
  // parameters from the route and/or request as needed.
    return AccessResult::allowedIf($account->hasPermission('create pqrsd content'));
  }

}
