<?php

/**
 * @file
 * Provides Drupal\breakfast\BreakfastInterface
 */
namespace Drupal\breakfast;
use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for ice cream flavor plugins
 * @author nguyeth
 *
 */
interface BreakfastInterface extends PluginInspectionInterface {
  /**
   * Return the name of the ice cream flavor
   * @return string
   */
  public function getName();
  
  /**
   * Return the price per scoop of the ice cream flavor
   * @return float
   */
  public function getPrice();
  
  /**
   * A slogan for the ice cream flavor.
   * @return string
   */
  public function slogan();
  
}