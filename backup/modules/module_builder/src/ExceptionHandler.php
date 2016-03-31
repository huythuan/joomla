<?php

/**
 * @file
 * Contains \Drupal\module_builder\ExceptionHandler.
 */

namespace Drupal\module_builder;

use ModuleBuilder\Exception\SanityException;

/**
 * Handles exceptions from the library and outputs messages.
 */
class ExceptionHandler {

  /**
   * Handle a sanity exception from the library and output a message.
   *
   * @param ModuleBuilder\Exception\SanityException $e
   *  A sanity exception object.
   */
  public static function handleSanityException(SanityException $e) {
    $failed_sanity_level = $e->getFailedSanityLevel();
    switch ($failed_sanity_level) {
      case 'data_directory_exists':
        drupal_set_message(t("The component data directory could not be created or is not writable."), 'error');
        break;
      case 'component_data_processed':
        drupal_set_message(t("Component data is not available. Use the 'Process data' tab to get data about hooks and plugins."), 'error');
        break;
    }
  }

}