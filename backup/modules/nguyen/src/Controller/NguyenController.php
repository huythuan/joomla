<?php
namespace Drupal\nguyen\Controller;

use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Controller\ControllerBase;
class NguyenController extends ControllerBase {
  public function inject() {
    return new JsonResponse('Hello');
  }
}