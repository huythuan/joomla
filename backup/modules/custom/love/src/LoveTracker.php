<?php
namespace Drupal\love;

use Drupal\Core\State\StateInterface;

class LoveTracker {
  protected $state;
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }
  public function addLove($target_name) {
    $this->state->set('love.last_recipient', $target_name);
    return $this;
  }
  public function getLastRecipient() {
    return $this->state->get('love.last_recipient');
  }
  
}