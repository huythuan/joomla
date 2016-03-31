<?php

namespace Drupal\migrate_joomla\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class MigrateJoomlaConfigForm extends ConfigFormBase {
  public function getFormId() {
    return 'migrate_joomla_config';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $weight = -20;
    $config = $this->config('migrate_joomla.settings');
   
    $form['joomla_settings_database'] = [
        '#type' => 'fieldset',
        '#weight' => $weight,
        '#title' => t('Joomla settings'),
        '#collapsible' => TRUE,
        '#collapsed' => FALSE
    ];
    
    $form['joomla_settings_database']['joomla_database_host'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Joomla database host name'),
      '#default_value' => $config->get('joomla_database_host'),
      '#description' => 'Host name of Joomla database server.'
    ];
    
    $form['joomla_settings_database']['joomla_database_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Joomla database name'),
        '#default_value' => $config->get('joomla_database_name'),
        '#description' => 'Joomla database.'
    ];
    
    $form['joomla_settings_database']['joomla_database_user'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Joomla database user name'),
        '#default_value' => $config->get('joomla_database_user'),
        '#description' => 'User name for Joomla database server.'
    ];
    
    $form['joomla_settings_database']['joomla_database_pass'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Joomla database password'),
        '#default_value' => $config->get('joomla_database_pass'),
        '#description' => 'Password for Joomla database server.'
    ];
    $form['joomla_settings_database']['advanced'] = array(
        '#type' => 'fieldset',
        '#title' => t('Advanced database settings'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE
    );
    $form['joomla_settings_database']['advanced']['joomla_database_port'] = array(
        '#type' => 'textfield',
        '#title' => 'Joomla database port number',
        '#default_value' =>  $config->get('joomla_database_port'),
        '#description' => 'Port number for Joomla database server.  The default (3306) is usually fine.'
    );
    $form['joomla_settings_database']['advanced']['joomla_prefix'] = array(
        '#type' => 'textfield',
        '#title' => 'Table Prefix of Joomla',
        '#default_value' =>  $config->get('joomla_prefix'),
        '#description' => 'Table Prefix for Joomla tables.'
    );
    $form['joomla_settings_database']['joomla_path'] = array(
        '#type' => 'textfield',
        '#title' => 'Path of your Joomla installation',
        '#default_value' =>  $config->get('joomla_path'),
        '#description' => 'The path name where you install Joomla. Example:<br />'
        . '<ul><li>Apache: <b>/home/YOUR_USERNAME/public_html/joomla</b></li>'
        . '<li>Windows using WAMP: <b>c:/wamp/www/joomla</b></li>'
        . '<li>Windows using IIS: <b>C:/Inetpub/wwwroot/joomla</b></li></ul>'
    );
    
    $form['joomla_settings_database']['joomla_category'] = array(
        '#type' => 'textfield',
        '#title' => 'Default category',
        '#default_value' =>  $config->get('joomla_category'),
        '#description' => 'Default category for Drupal site'       
    );

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('migrate_joomla.settings');
    $config->set('joomla_database_host', $form_state->getValue('joomla_database_host'));
    $config->set('joomla_database_name', $form_state->getValue('joomla_database_name'));
    $config->set('joomla_database_user', $form_state->getValue('joomla_database_user'));
    $config->set('joomla_database_pass', $form_state->getValue('joomla_database_pass'));
    $config->set('joomla_database_port', $form_state->getValue('joomla_database_port'));
    $config->set('joomla_prefix', $form_state->getValue('joomla_prefix'));
    $config->set('joomla_path', $form_state->getValue('joomla_path'));
    $config->set('joomla_category', $form_state->getValue('joomla_category'));
    $config->save();
  }

  public function getEditableConfigNames() {
    return ['migrate_joomla.settings'];
  }
}
