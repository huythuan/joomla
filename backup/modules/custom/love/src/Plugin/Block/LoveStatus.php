<?php
namespace Drupal\love\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\love\LoveTracker;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Wikimedia\Composer\Merge\StabilityFlags;

/**
 * Reports on lovability status.
 * 
 * @Block(
 *   id = "love_status",
 *   admin_label = @Translation("Love status"),
 *   category = @Translation("System")
 * )
 */
class LoveStatus extends BlockBase implements ContainerFactoryPluginInterface {
  protected $loveTracker;
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoveTracker $loveTracker) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->loveTracker = $loveTracker;
  }
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
        $configuration, $plugin_id, $plugin_definition,
        $container->get('love.love_tracker')
    );
  }
  public function defaultConfiguration() {
    return ['enabled' => 1];
  }
  public function blockForm($form, FormStateInterface $form_state) {
    $form['enabled'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Loving enabled'),
        '#default_value' => $this->configuration['enabled'],      
    ];
    return $form;
  }
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['enabled'] = (bool)$form_state->getValue('enabled');
  }
  public function build() {
    if ($this->configuration['enabled']) {
      $message = $this->t('@to was the last person loved', [
          '@to' => $this->loveTracker->getLastRecipient()
      ]);
    }
    else {
      $message = $this->t('No loves :-(');
    }
    return [
        '#markup' => $message,
    ];
  }
  
}
