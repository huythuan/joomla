<?php
/**
 * @file
 * Definition of Drupal\country\Plugin\field\formatter\CountryDefaultFormatter.
 */
namespace Drupal\country\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
/**
 * Plugin implementation of the 'country' formatter.
 * @FieldFormatter(
 *   id = "country_default",
 *   module = "country",
 *   label = @Translation("Country"),
 *   field_types = {
 *     "country"
 *   }
 * )
 */
class CountryDefaultFormatter extends FormatterBase {
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();
    $countries = \Drupal::service('country_manager')->getList();
    foreach ($items as $delta => $item) {
      if(isset($countries[$item->value])) {
        $elements[$delta] = array('#markup' => $countries[$item->value]);
      }
    }
    return $elements;
  }
}