<?php
namespace Drupal\love\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\love\LoveTracker;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\node\Entity\Node;


class LoveController extends ControllerBase {
  protected $loveTracker;
  
  public function __construct(LoveTracker $tracker) {
    $this->loveTracker = $tracker; 
  }
  public static function create(ContainerInterface $container) {
    return new static($container->get('love.love_tracker')); 
  }
  public function love($from, $to, $count) {
    $this->loveTracker->addLove($to);
    return [
        '#theme' => 'love_page',
        '#from' => $from,
        '#to' => $to,
        '#count' => $count ?: $this->config('love_settings')->get('default_count'),
    ];
  }
  public function love2($from, $to, $count) {
  if (!$count) {
    $count = $this->config('love.settings')->get('default_count');
  }
  return [
    '#theme' => 'love_page',
    '#from' => $from,
    '#to' => $to,
    '#count' => $count
  ];  
  }
  public function nodeLove(NodeInterface $node) {
    if($node->isPublished()) {
      $body = $node->body->value = 'Change the content';
      return [
          '#title' => $node->label() . 'Test',
          '#markup' => $body
      ];
    }
    return ['#markup' => $body];
  }
}