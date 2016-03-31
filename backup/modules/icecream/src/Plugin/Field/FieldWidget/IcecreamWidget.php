<?php

/**
 * @file
 * Contains \Drupal\icecream\Plugin\field\widget\TextWidget.
 */

namespace Drupal\icecream\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'icecream_text' widget.
 *
 * @FieldWidget(
 *   id = "icecream_text",
 *   module = "icecream",
 *   label = @Translation("Icecream value"),
 *   field_types = {
 *     "icecream_rgb"
 *   }
 * )
 */
class IcecreamWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = isset($items[$delta]->value) ? $items[$delta]->value : '';
    $options = array();
    $icecream_items = \Drupal::service('plugin.manager.icecream')->getDefinitions();
    foreach ($icecream_items as $plugin_id => $icecream_item) {
      $options[$plugin_id] = $icecream_item['name'];
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
