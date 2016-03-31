<?php
/**
 * Plugin implementation of the 'country' field type.
 * @FieldType(
 *   id = "country",
 *   label = @Translation("Country"),
 *   description = @Translation("Stores the ISO-2 name of a country."),
 *   category = @Translation("Custom"),
 *   default_widget = "country_default",
 *   default_formatter = "country_default"
 * 
 * )
 *
 */
namespace Drupal\country\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

class CountryItem extends FieldItemBase {
  const COUNTRY_ISO2_MAXLENGTH = 2;
  /**
   * {@inheritdoc}
   * @param FieldStorageDefinitionInterface $field_definition
   * @return string[][][]|boolean[][][]
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
          'value' => array(
              'type' => 'char',
              'length' => static::COUNTRY_ISO2_MAXLENGTH,
              'not null' => FALSE,
          ),
      ),
      'indexes' => array(
          'value' => array('value'),
      ),
    );
  }
  /**
   * {@inheritdoc}
   * @param FieldStorageDefinitionInterface $field_definition
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')->setLabel(t('Country'));
    return $properties;
  }
  /**
   * 
   * {@inheritDoc}
   * @see \Drupal\Core\TypedData\Plugin\DataType\Map::isEmpty()
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }
  /**
   * 
   * {@inheritDoc}
   * @see \Drupal\Core\TypedData\TypedData::getConstraints()
   */
  public function getConstraints() {
    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints = parent::getConstraints();
    $constraints[] = $constraint_manager->create('ComplexData', array(
        'value' => array(
            'Length' => array(
                'max' => static::COUNTRY_ISO2_MAXLENGTH,
                'maxMessage' => t('%name: the country iso-2 code may not be longer
                    than @max characters.', array(
                        '%name' => $this->getFieldDefinition()->getLabel(),
                        '@max' => static::COUNTRY_ISO2_MAXLENGTH
                    )),
            )
        ),
    ));
    return $constraints;
  }
}