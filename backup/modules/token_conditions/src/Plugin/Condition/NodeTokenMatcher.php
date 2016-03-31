<?php
/**
 * @file 
 * Contain Drupal\token_conditions\Plugin\Condition\TokenMatcher
 */
namespace Drupal\token_conditions\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\token_conditions\Plugin\Condition\TokenMatcher;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Provides a 'Token Matcher' condition.
 * @Condition(
 *   id = "node_token_matcher",
 *   label = @Translation("Node Token Matcher"),
 *   context = {
 *     "node" = @ContextDefinition("entity:node", label = @Translation("Node"))
 *   }
 * )
 */
class NodeTokenMatcher extends ConditionPluginBase implements ContainerFactoryPluginInterface {

/**
   * {@inheritdoc}
   */
   public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->setConfiguration($configuration);
   }
    /**
     * 
     */
    public function evaluate() {
        $token_service = \Drupal::token();
        $node = $this->getContextValue('node');
        $token_data = ['node' => $node];
        $token_replaced = $token_service->replace($this->configuration['token_value'], $token_data);
        $check_replaced = $token_service->replace($this->configuration['check_value'], $token_data);
        return  $token_replaced == $check_replaced;
    }

    public function summary() {
        
    }

    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
      return new static (
        $configuration,
        $plugin_id,
        $plugin_definition);  
    }
/**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
   $form['token_value'] = array (
     '#type' => 'textfield',
     '#title' => $this->t('Token String to check'),
     '#default_value' => $this->configuration['token_value'],
   );
   $form['check_value'] = array (
     '#type' => 'textfield',
     '#title' => $this->t('Other string to check'),
     '#default_value' => $this->configuration['check_value'],
   );
   $form['token_tree'] = array(
    '#theme' => 'token_tree',
    '#token_types' => array(),
    '#dialog' => TRUE,
   );
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['token_value'] = $form_state->getValue('token_value');
    $this->configuration['check_value'] = $form_state->getValue('check_value');
    parent::submitConfigurationForm($form, $form_state);
  }

}