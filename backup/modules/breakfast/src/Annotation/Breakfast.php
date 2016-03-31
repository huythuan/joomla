<?php 

/**
 * @file 
 * Contains \Drupal\icecream\Annotation\Breakfast.
 */

namespace Drupal\breakfast\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a flavor item annotation object.
 * 
 * @author nguyeth
 * 
 * @Annotation
 *
 */
class Breakfast extends Plugin {
  /**
   * The plugin ID
   * @var string
   */
  public $id;
  
  /**
   * The name of the flavor
   * @var string
   */
  public $name;
  
  /**
   * The price of one scoop of the flavor in dollars
   * @var float
   */
  public $price;
  
  
}