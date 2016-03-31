<?php 

/**
 * @file 
 * Contains \Drupal\breakfast\Plugin\Breakfast\Idly.
 */

namespace Drupal\breakfast\Plugin\Breakfast;

use Drupal\breakfast\BreakfastBase;

/**
 * Provides a 'idly' flavor
 * @Breakfast(
 *   id = "idly",
 *   name = @Translation("Idly"),
 *   price = 1.75
 * )
 * @author nguyeth
 *
 */
class Idly extends BreakfastBase {
  public function slogan() {
    return t('The other best flavor');
  }
}