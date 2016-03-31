<?php
/**
 * @file
 * Contains \Drupal\basic\Controller\BasicController.
 */
namespace Drupal\basic\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * BasicController.
 */
class BasicController extends ControllerBase {
  /**
   * Generates an example page.
   */
  public function basic() {
    return array(
      '#markup' => t('Hello Basic'),
    );
  }
  
}
