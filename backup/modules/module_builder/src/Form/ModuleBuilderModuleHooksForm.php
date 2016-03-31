<?php
/**
 * @file
 * Contains \Drupal\module_builder\Form\ModuleBuilderModuleHooksForm.
 */

namespace Drupal\module_builder\Form;

use Drupal\Core\Render\Element;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\module_builder\ExceptionHandler;
use Drupal\module_builder\LibraryWrapper;
use ModuleBuilder\Exception\SanityException;


/**
 * Class ModuleBuilderModuleHooksForm
 *
 * Form for selecting hooks.
 */
class ModuleBuilderModuleHooksForm extends ModuleBuilderComponentFormBase {

   /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $module = $this->entity;

    $data = $module->get('data');

    LibraryWrapper::loadLibrary();

    // Get the Task handler.
    try {
      $mb_task_handler_report = \ModuleBuilder\Factory::getTask('ReportHookData');
    }
    catch (SanityException $e) {
      ExceptionHandler::handleSanityException($e);
      return $form;
    }

    // Call a method in the Task handler to perform the operation.
    $hook_info = $mb_task_handler_report->listHookOptionsStructured();
    // Need to catch \ModuleBuilder\Exception!

    // Create a fieldset for each group, containing checkboxes.
    foreach ($hook_info as $group => $hook_group_info) {
      $form[$group] = array(
        '#type' => 'details',
        '#title' => $group,
        //'#open' => TRUE,
      );

      $hook_names = array_keys($hook_group_info);

      // Need to differentiate the key, otherwise FormAPI treats this as an
      // error on submit.
      $form[$group][$group . '_hooks'] = array(
        '#type' => 'checkboxes',
        '#options' => array_combine($hook_names, $hook_names),
        '#default_value' => array_intersect($hook_names, $data['hooks']),
      );

      foreach ($hook_group_info as $hook => $hook_info_single) {
        // External Uri.
        $url = Url::fromUri('https://api.drupal.org/api/function/' . $hook . '/8');
        $form[$group][$group . '_hooks'][$hook]['#description'] =
          $hook_info_single['description'] . ' ' .
          \Drupal::l(t('[documentation]'), $url);
      }
    }

    $form_state->set('module_builder_groups', array_keys($hook_info));

    return $form;
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

    // We can't just iterate over $values, because of a core bug with
    // EntityForm, so we need to know which keys to look at.
    // See https://www.drupal.org/node/2665714.
    $groups = $form_state->get('module_builder_groups');

    $hooks = [];
    foreach ($groups as $group) {
      $group_values = $values[$group . '_hooks'];
      // Filter out empty values. (FormAPI *still* doesn't do this???)
      $group_hooks = array_filter($group_values);
      $value = array_filter($value);
      // Store as a numeric array.
      $group_hooks = array_keys($group_hooks);

      $hooks += $group_hooks;
    }

    $data = $entity->get('data');
    $data['hooks'] = $hooks;
    $entity->set('data', $data);
  }

}
