<?php
namespace Drupal\country\Plugin\Field\FieldWidget;

use Drupal;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
/**
 * Plugin implementation of the 'country_default' widget.
 * 
 * @FieldWidget (
 *   id = "country_default",
 *   label = @Translation("Country select"),
 *   field_types = {
 *     "country"
 *   }
 * )
 *
 */
class CountryDefaultWidget extends WidgetBase {
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $countries = \Drupal::service('country_manager')->getList();
    $element['value'] = $element + array(
        '#type' => 'select',
        '#options' => $countries,
        '#empty_value' => '',
        '#default_value' => (isset($items[$delta]->value) && isset($countries[$items[$delta]->value]))
        ? $items[$delta]->value : NULL,
        '#description' => t('Select a country')
    );
    return $element;
  }
  
}