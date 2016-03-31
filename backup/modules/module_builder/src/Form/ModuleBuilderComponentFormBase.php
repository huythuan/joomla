<?php
/**
 * @file
 * Contains \Drupal\module_builder\Form\ModuleBuilderComponentFormBase.
 */

namespace Drupal\module_builder\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\module_builder\LibraryWrapper;
use ModuleBuilder\Exception\SanityException;

/**
 * Class ModuleBuilderComponentFormBase
 *
 * Base class for Module Builder component forms.
 */
class ModuleBuilderComponentFormBase extends EntityForm {

  /**
   * Add form elements for the specified component properties.
   *
   * @param $form
   *  The form array.
   * @param FormStateInterface $form_state
   *  The form state object.
   * @param $component_properties_to_use
   *  An array of property names from the component.
   *
   * @return
   *  The form array.
   */
  protected final function componentPropertiesForm($form, FormStateInterface $form_state, $component_properties_to_use) {
    // Set #tree on the data element.
    $form['data'] = [
      '#tree' => TRUE,
    ];

    $module = $this->entity;
    $module_entity_data = $module->get('data');

    // Load our library.
    LibraryWrapper::loadLibrary();

    // Need to catch \ModuleBuilder\Exception!

    // Get the component data info.
    try {
      $mb_task_handler_generate = \ModuleBuilder\Factory::getTask('Generate', 'module');
    }
    catch (SanityException $e) {
      drupal_set_message(t("Component data is not available. Use the 'Process data' tab to get data about hooks and plugins."), 'error');
    }

    $component_data_info = $mb_task_handler_generate->getRootComponentDataInfo();

    $component_data = array();

    // Work through the component data info, assembling the component data array
    // Each property info needs to be prepared, so iterate by reference.
    foreach ($component_data_info as $property_name => &$property_info) {
      // Prepare the single property: get options, default value, etc.
      // (We prepare all of them as they may have an effect on the default value
      // of a property we show in this form.)
      $mb_task_handler_generate->prepareComponentDataProperty($property_name, $property_info, $component_data);

      // Skip any we're not editing on this tab.
      if (!in_array($property_name, $component_properties_to_use)) {
        continue;
      }

      // Get the value to set in the form element.
      $form_default_value = isset($module_entity_data[$property_name])
        ? $module_entity_data[$property_name]
        : $component_data[$property_name];

      $element = array(
        '#title' => $property_info['label'],
        '#required' => $property_info['required'],
      );

      if (isset($property_info['description'])) {
        $element['#description'] = $property_info['description'];
      }

      // The type of the form element depends on the format of the component data
      // property.
      if ($property_info['format'] == 'array') {
        if (isset($property_info['options'])) {
          $element['#type'] = 'checkboxes';
          $element['#options'] = $property_info['options'];

          $form_default_value = array_combine($form_default_value, $form_default_value);
        }
        else {
          $element['#type'] = 'textarea';
          $element['#description'] = (string) $element['#description'] . ' ' . t("Enter one item per line.");

          $form_default_value = implode("\n", $form_default_value);
        }
      }
      elseif ($property_info['format'] == 'boolean') {
        $element['#type'] = 'checkbox';
      }
      else {
        $element['#type'] = 'textfield';
      }

      $element['#default_value'] = $form_default_value;
      $element['#mb_format'] = $property_info['format'];

      $form['data'][$property_name] = $element;
    }

    return $form;
  }

  /**
   * Returns an array of supported actions for the current entity form.
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#dropbutton' => 'mb',
      // Still no way to get a button's name, apparently?
      '#mb_action' => 'submit',
      '#submit' => array('::submitForm', '::save'),
    );
    if ($this->getNextLink() != 'generate-form') {
      $actions['submit_next'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Save and go to next page'),
        '#dropbutton' => 'mb',
        '#mb_action' => 'submit_next',
        '#submit' => array('::submitForm', '::save'),
      );
    }
    $actions['submit_generate'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save and generate code'),
      '#dropbutton' => 'mb',
      '#mb_action' => 'submit_generate',
      '#submit' => array('::submitForm', '::save'),
    );

    return $actions;
  }

  /**
   * Copies top-level form values to entity properties
   *
   * This should not change existing entity properties that are not being edited
   * by this form.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity the current form should operate upon.
   * @param array $form
   *   A nested array of form elements comprising the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  protected function copyFormValuesToEntity(EntityInterface $entity, array $form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    if ($this->entity instanceof EntityWithPluginCollectionInterface) {
      // Do not manually update values represented by plugin collections.
      $values = array_diff_key($values, $this->entity->getPluginCollections());
    }

    //dsm($form);

    if ($this->entity->isNew()) {
      $data = [];
    }
    else {
      // Add to the existing entity data array.
      $data = $entity->get('data');
    }

    foreach ($values['data'] as $key => $value) {
      $form_element = $form['data'][$key];
      if ($form_element['#type'] == 'textarea') {
        if ($form_element['#mb_format'] == 'array') {
          // Only explode a non-empty string, as explode() will turn '' into an
          // array!
          if (!empty($value)) {
            $value = explode("\n", $value);
          }
        }
      }
      if ($form_element['#type'] == 'checkboxes') {
        // Filter out empty values. (FormAPI *still* doesn't do this???)
        $value = array_filter($value);
        // Don't store values also in the keys, as some of these have dots in
        // them, which ConfigAPI doesn't allow.
        $value = array_keys($value);
      }

      if (empty($value)) {
        unset($data[$key]);
      }
      else {
        $data[$key] = $value;
      }
    }
    $entity->set('data', $data);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $module = $this->entity;

    $status = $module->save();

    if ($status) {
      // Setting the success message.
      drupal_set_message($this->t('Saved the module: @name.', array(
        '@name' => $module->name,
      )));
    }
    else {
      drupal_set_message($this->t('The @name module was not saved.', array(
        '@name' => $module->name,
      )));
    }

    // Optionally advance to next tab or go to the generate page.
    $element = $form_state->getTriggeringElement();
    switch ($element['#mb_action']) {
      case 'submit':
        break;
      case 'submit_next':
        $next_link = $this->getNextLink();
        $url = $module->toUrl($next_link);
        $form_state->setRedirectUrl($url);
        break;
      case 'submit_generate':
        $url = $module->toUrl('generate-form');
        $form_state->setRedirectUrl($url);
        break;
    }
  }

  /**
   * Get the next entity link after the one for the current form.
   *
   * @return
   *  The name of an entity link.
   */
  protected function getNextLink() {
    // Probably a more elegant way of figuring out where we currently are
    // with routes maybe?
    $operation = $this->getOperation();

    // Special case for add form.
    if ($operation == 'default') {
      $operation = 'edit';
    }

    $operation_relationship = $operation . '-form';
    $entity_relationships = $this->entity->uriRelationships();
    $index = array_search($operation_relationship, $entity_relationships);

    return $entity_relationships[$index + 1];
  }

}
