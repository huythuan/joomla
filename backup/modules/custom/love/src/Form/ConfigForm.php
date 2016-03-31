<?php

namespace Drupal\love\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfigForm extends ConfigFormBase {
  public function getFormId() {
    return 'love_config';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('love.settings');

    $form['default_count'] = [
      '#type' => 'number',
      '#title' => $this->t('Default love count'),
      '#default_value' => $config->get('default_count'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('love.settings');
    $config->set('default_count', $form_state->getValue('default_count'));
    $config->save();
  }

  public function getEditableConfigNames() {
    return ['love.settings'];
  }
}
