<?php

/**
 * @file
 * Contains Drupal\breakfast\Plugin\Field\FieldFormatter\BreakfastFormatter.
 */

namespace Drupal\breakfast\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'breakfast_simple_text' formatter.
 *
 * @FieldFormatter(
 *   id = "breakfast_simple_text",
 *   module = "breakfast",
 *   label = @Translation("Simple breakfast formatter"),
 *   field_types = {
 *     "breakfast_rgb"
 *   }
 * )
 */
class BreakfastFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    
    foreach ($items as $delta => $item) {
     // $breakfast_item = \Drupal::service('plugin.manager.breakfast')->createInstance('uppuma');
      //$markup = '<h1> Hello </h1>';
      //print_r($item->value);
      //$markup = '<h1>'. $breakfast_item->getName() . '</h1>';
      //$markup .= '<h2> Price:'. $icecream_item->getPrice() . '</h2>';
      $elements[$delta] = array(
          '#markup' => $markup,
      );
    }
    
    return $elements;
  }

}
