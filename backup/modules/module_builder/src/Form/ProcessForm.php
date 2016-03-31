<?php

/**
 * @file
 * Contains \Drupal\module_builder\Form\ProcessForm
 */
namespace Drupal\module_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\module_builder\ExceptionHandler;
use ModuleBuilder\Exception\SanityException;

/**
 * TODO  .
 */
class ProcessForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'module_builder_process';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    \Drupal\module_builder\LibraryWrapper::loadLibrary();

    try {
      $mb_task_handler_report = \ModuleBuilder\Factory::getTask('ReportHookDataFolder');
    }
    catch (SanityException $e) {
      ExceptionHandler::handleSanityException($e);
      return $form;
    }

    // The task handler returns sane values for these even if there's no hook
    // data.
    $last_update = $mb_task_handler_report->lastUpdatedDate();
    $doc_files = $mb_task_handler_report->listHookFiles();
    $directory = \ModuleBuilder\Factory::getEnvironment()->getHooksDirectory();

    $form['intro'] = array(
      '#markup' => '<p>' . t("Module builder analyses your current code to find data about Drupal components such as hooks and plugins. This processed data is stored in your local filesystem.") . '</p>',
    );

    $form['last_update'] = array(
      '#markup' => '<p>' . (
        $last_update ?
          t('Your last data update was %date.', array(
            '%date' => format_date($last_update, 'large'),
          )) :
          t('The hook documentation has not yet been downloaded.')
        ) . '</p>',
    );

    if (count($doc_files)) {
      $form['files_text'] = array(
        '#markup' => '<p>' . t('You have the following hook definition files saved in %dir: ', array(
          '%dir' => $directory,
        )) . '</p>',
      );
      $form['files'] = [
        '#theme' => 'item_list',
        '#items' => $doc_files,
      ];
    }

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Update data'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal\module_builder\LibraryWrapper::loadLibrary();

    // Safe to do this without exception handling: it's already been checked in
    // the form builder.
    $mb_task_handler_collect = \ModuleBuilder\Factory::getTask('Collect');
    $mb_task_handler_collect->collectComponentData();
  }

}