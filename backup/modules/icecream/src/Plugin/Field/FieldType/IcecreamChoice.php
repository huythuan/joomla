<?php

/**
 * @file
 * Contains Drupal\icecream\Plugin\Field\FieldType\IcecreamChoice.
 */

namespace Drupal\icecream\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'field_example_rgb' field type.
 *
 * @FieldType(
 *   id = "icecream_rgb",
 *   label = @Translation("Icecream"),
 *   module = "icecream",
 *   description = @Translation("Demonstrates a field composed of an RGB breakfast."),
 *   default_widget = "icecream_text",
 *   default_formatter = "icecream_simple_text"
 * )
 */
class IcecreamChoice extends FieldItemBase {
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
      ->setLabel(t('Icecream value'));

    return $properties;
  }
}
