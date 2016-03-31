<?php 

/**
 * @file 
 * Contains \Drupal\breakfast\Plugin\Breakfast\Uppuma.
 */

namespace Drupal\breakfast\Plugin\Breakfast;

use Drupal\breakfast\BreakfastBase;

/**
 * Provides a 'Uppuma' flavor
 * @Breakfast(
 *   id = "uppuma",
 *   name = @Translation("Uppuma"),
 *   price = 1.78
 * )
 * @author nguyeth
 *
 */
class Uppuma extends BreakfastBase {
  public function slogan() {
    return t('The other best flavor');
  }
}