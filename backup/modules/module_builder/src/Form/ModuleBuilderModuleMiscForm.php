<?php
/**
 * @file
 * Contains \Drupal\module_builder\Form\ModuleBuilderModuleMiscForm.
 */

namespace Drupal\module_builder\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class ModuleBuilderModuleMiscForm
 *
 * Form for selecting other components to implement.
 */
class ModuleBuilderModuleMiscForm extends ModuleBuilderComponentFormBase {

   /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    // List of component properties this form uses.
    $component_properties_to_use = [
      'module_help_text',
      // Not ready for D8 yet.
      //'settings_form',
      'permissions',
      'services',
      'router_items',
    ];
    $form = $this->componentPropertiesForm($form, $form_state, $component_properties_to_use);

    // Change the help text form element to a textarea.
    $form['data']['module_help_text']['#type'] = 'textarea';

    return $form;
  }

}
