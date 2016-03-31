<?php 

/**
 * @file 
 * Contains \Drupal\icecream\Plugin\Flavor\Chocolate.
 */

namespace Drupal\icecream\Plugin\Flavor;

use Drupal\icecream\FlavorBase;

/**
 * Provides a 'sweet' flavor
 * @Flavor(
 *   id = "sweet",
 *   deriver = "Drupal\icecream\Plugin\Derivative\Sweets"
 * )
 * @author nguyeth
 *
 */
class Sweet extends FlavorBase {
  public function slogan() {
    return t('The other best flavor');
  }
}