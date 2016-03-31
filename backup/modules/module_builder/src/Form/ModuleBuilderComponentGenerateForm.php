<?php
/**
 * @file
 * Contains \Drupal\module_builder\Form\ModuleBuilderComponentGenerateForm.
 */

namespace Drupal\module_builder\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\module_builder\ExceptionHandler;
use Drupal\module_builder\LibraryWrapper;
use ModuleBuilder\Exception\SanityException;

/**
 * Class ModuleBuilderComponentGenerateForm
 *
 * Form showing generated component code.
 */
class ModuleBuilderComponentGenerateForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    // Load our library.
    LibraryWrapper::loadLibrary();

    $module = $this->entity;
    $component_data = $module->get('data');
    //dsm($component_data);

    // Add in the component root name and readable name.
    $component_data['root_name'] = $module->id;
    $component_data['readable_name'] = $module->label;

    try {
      // Get our task handler.
      $mb_task_handler_generate = \ModuleBuilder\Factory::getTask('Generate', 'module');
    }
    catch (SanityException $e) {
      ExceptionHandler::handleSanityException($e);
      return $form;
    }

    // Get the component data info.
    $component_data_info = $mb_task_handler_generate->getRootComponentDataInfo();

    // Perform any final processing on the component data.
    // This prepares data, for example expands options such as hook presets.
    $mb_task_handler_generate->processComponentData($component_data_info, $component_data);

    // Build list.
    // The UI always gets the full code.
    $component_data['requested_build'] = array('code' => TRUE, 'info' => TRUE);

    // Get the files.
    $files = $mb_task_handler_generate->generateComponent($component_data);

    $form['code'] = array(
      '#type' => 'vertical_tabs',
    );

    foreach ($files as $filename => $code) {
      $form[$filename] = array(
        '#type' => 'details',
        '#title' => $filename,
        '#group' => 'code',
      );

      $form[$filename]['code_instructions_' . $filename] = array(
        '#markup' => t('Please copy and paste the following text into a file called !module.', array('!module' => $filename)),
        '#prefix' => '<div class="module-message">',
        '#suffix' => '</div>',
      );
      $form[$filename]['module_code_'  . $filename] = array(
        '#type' => 'textarea',
        '#title' => t($filename .' code'),
        '#rows' => count(explode("\n", $code)),
        '#default_value' => $code,
        '#prefix' => '<div class="module-code">',
        '#suffix' => '</div>',
      );
    }

    return $form;
  }

  /**
   * Override to remove actions.
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    return [];
  }

}
