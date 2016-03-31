<?php
/**
 * @file 
 * Provides Drupal\breakfast\BreakfastBase.
 */
namespace  Drupal\breakfast;
use Drupal\Component\Plugin\PluginBase;

class BreakfastBase extends PluginBase implements FlavorInterface {
  public function getName() {
    return $this->pluginDefinition['name'];
  }
  public function getPrice() {
    return $this->pluginDefinition['price'];
  }
  public function slogan() {
    return t('Best flovar ever.');
  }
}
