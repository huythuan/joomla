<?php
/**
 * @file
 * Contains \Drupal\module_builder\LibraryWrapper.
 */

namespace Drupal\module_builder;

use Drupal\module_builder\Environment\Drupal8Module;

/**
 * Class LibraryWrapper
 *
 * Quick and dirty wrapper class to load our library.
 *
 * TODO: Use either Libraries module or Composer manager.
 */
class LibraryWrapper {

  /**
   * Load the Module Builder library and set the environment.
   *
   * @throws
   *  Throws an exception if the library can't be found.
   */
  public function loadLibrary() {
    // Stick in libraries so Drupal doesn't pick up 2nd copy of module.
    // This means I can symlink the normal git clone in sandbox.
    if (!file_exists('libraries/module_builder/Factory.php')) {
      throw new \Exception("Mising library.");
    }
    require_once('libraries/module_builder/Factory.php');

    \ModuleBuilder\Factory::setEnvironmentClass('BasicLibrary', \Drupal::VERSION);
  }

}
