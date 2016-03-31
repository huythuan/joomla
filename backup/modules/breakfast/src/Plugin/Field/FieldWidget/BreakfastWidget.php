<?php

/**
 * @file
 * Contains \Drupal\breakfast\Plugin\field\widget\TextWidget.
 */

namespace Drupal\breakfast\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'breakfast_text' widget.
 *
 * @FieldWidget(
 *   id = "breakfast_text",
 *   module = "breakfast",
 *   label = @Translation("Breakfast value as #ffffff"),
 *   field_types = {
 *     "breakfast_rgb"
 *   }
 * )
 */
class BreakfastWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = isset($items[$delta]->value) ? $items[$delta]->value : '';
    $options = array();
    $breakfast_items = \Drupal::service('plugin.manager.breakfast')->getDefinitions();
    foreach ($breakfast_items as $plugin_id => $breakfast_item) {
      $options[$plugin_id] = $breakfast_item['name'];
    }
    $element += array(
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $value,
      '#multiple' => FALSE,
      '#element_validate' => array(
        array($this, 'validate'),
      ),
    );
    return array('value' => $element);
  }

  /**
   * Validate the color text field.
   */


}
