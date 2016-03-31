<?php
namespace Drupal\basic\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Basic' block.
 * @Block(
 *   id = "basic_block",
 *   admin_label = @Translation("Basic block")
 * )
 */
class BasicBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    if (isset($config['basic_block_settings']) && !empty($config['basic_block_settings'])) {
      $saySomething = $config['basic_block_settings'];
    }
    else {
      $saySomething = 'Not Saying Yet';
    }
    return array(
      '#markup' => $this->t('System: @saySomething', array('@saySomething' => $saySomething)),
    );
  }
  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account, $return_as_object = FALSE) {
    $access = $this->blockAccess($account);
    return $return_as_object ? $access : $access->isAllowed();
  }
  /**
   * {@inheritdoc}
   * 
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['basic_block_settings'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Announcement'),
      '#description' => $this->t('Type anything to show on the block ?'),
      '#default_value' => $this->configuration['basic_block_settings'],
    ];
    return $form;
  }
  /**
   * @{inheritdoc}
   * @param type $form
   * @param FormStateInterface $form_state
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['basic_block_settings'] = $form_state->getValue('basic_block_settings');
  }

}

