<?php

/**
 * @file
 * Contains \Drupal\breakfast\BreakfastPluginManager.
 */

namespace Drupal\breakfast;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * A Plugin to manage your breakfast.
 */
class BreakfastPluginManager extends DefaultPluginManager {

  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/Breakfast', $namespaces, $module_handler, 'Drupal\breakfast\BreakfastInterface', 'Drupal\breakfast\Annotation\Breakfast');
  
    $this->alterInfo('breakfast_flavors_info');
    $this->setCacheBackend($cache_backend, 'breakfast_flavors');
  }

}
