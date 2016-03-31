<?php
use Drupal\Component\Plugin\Derivative\DeriverBase;
/**
 * Sweets are dynamic plugin definitions
 */
class Sweets extends DeriverBase {
  public function getDerivativeDefinitions($base_plugin_definition) {
    $sweets_list = drupal_get_path('module', 'icecream') . '/sweets.yml';
    $sweets = Yaml::decode(file_get_contents($sweets_list));
    foreach ($sweets as $key => $sweet) {
      $this->derivatives[$key] = $base_plugin_definition;
      $this->derivatives[$key] += array(
          'name' => $sweet['label'],
          'price' => $sweet['price']
      );
    }
    return $this->derivatives;
  }
}