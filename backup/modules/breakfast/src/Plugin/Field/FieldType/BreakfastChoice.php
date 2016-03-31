<?php

/**
 * @file
 * Contains Drupal\breakfast\Plugin\Field\FieldType\BreakfastChoice.
 */

namespace Drupal\breakfast\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'field_example_rgb' field type.
 *
 * @FieldType(
 *   id = "breakfast_rgb",
 *   label = @Translation("Breakfast RGB"),
 *   module = "breakfast",
 *   description = @Translation("Demonstrates a field composed of an RGB breakfast."),
 *   default_widget = "breakfast_text",
 *   default_formatter = "breakfast_simple_text"
 * )
 */
class BreakfastChoice extends FieldItemBase {
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'text',
          'size' => 'tiny',
          'not null' => FALSE,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Breakfast value'));

    return $properties;
  }
}
