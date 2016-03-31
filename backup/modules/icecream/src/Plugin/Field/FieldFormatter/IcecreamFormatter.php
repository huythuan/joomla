<?php

/**
 * @file
 * Contains Drupal\icecream\Plugin\Field\FieldFormatter\IcecreamFormatter.
 */

namespace Drupal\icecream\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'icecream_simple_text' formatter.
 *
 * @FieldFormatter(
 *   id = "icecream_simple_text",
 *   module = "icecream",
 *   label = @Translation("Simple icecream formatter"),
 *   field_types = {
 *     "icecream_rgb"
 *   }
 * )
 */
class IcecreamFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $elements = array();
    
    foreach ($items as $delta => $item) {
      $icecream_item = \Drupal::service('plugin.manager.icecream')->createInstance($item->value);
      $markup = '<h1>'. $icecream_item->getName() . '</h1>';
      $markup .= '<h2> Price:'. $icecream_item->getPrice() . '</h2>';
      $elements[$delta] = array(
          '#markup' => $markup,
      );
    }
    
    return $elements;
    
  }

}
