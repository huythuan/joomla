<?php
/**
 * @file
 * Contains \Drupal\basic\Form\BasicForm
 */
namespace Drupal\basic\Form;
use Drupal\Core\Form\FormBase;
class BasicForm extends FormBase {
  /**
   * @{inheritdoc}
   */
  public function getFormId() {
    return 'basic_form';
  }
  /**
   * @{inheritdoc}
   */
  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form['email'] = array(
      '#type' => 'email',
      '#title' => $this->t('Your email address')
    );
    $form['show'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    );
    return $form;
  }
  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    if (strpos($form_state->getValue('email'),'.com') ===FALSE) {
      $this->setFormError('email', $form_state, $this->t('This is not a .com email address.'));
    }
    parent::validateForm($form, $form_state);
  }
  /**
   * @{inheritdoc}
   */
  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    drupal_set_message($this->t('Your email is: @email', array('@email' => $form_state->getValue('email'))));
  }

}