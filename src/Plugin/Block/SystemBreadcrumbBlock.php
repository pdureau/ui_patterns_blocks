<?php

namespace Drupal\ui_patterns_blocks\Plugin\Block;

use Drupal\system\Plugin\Block\SystemBreadcrumbBlock as CoreSystemBreadcrumbBlock;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\ui_patterns\Form\PatternDisplayFormTrait;
use Drupal\ui_patterns\UiPatternsSourceManager;
use Drupal\ui_patterns_blocks\PatternBlockTrait;
use Drupal\ui_patterns\UiPatternsManager;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block to display the breadcrumbs.
 *
 * @Block(
 *   id = "ui_patterns_system_breadcrumb_block",
 *   category = @Translation("System (with UI Patterns)"),
 *   admin_label = @Translation("Breadcrumbs")
 * )
 */
class SystemBreadcrumbBlock extends CoreSystemBreadcrumbBlock {

  use PatternDisplayFormTrait, PatternBlockTrait {
    PatternBlockTrait::getDefaultValue insteadof PatternDisplayFormTrait;
  }

  /**
   * UI Patterns manager.
   *
   * @var \Drupal\ui_patterns\UiPatternsManager
   */
  protected $patternsManager;

  /**
   * UI Patterns source manager.
   *
   * @var \Drupal\ui_patterns\UiPatternsSourceManager
   */
  protected $sourceManager;

  /**
   * A module manager object.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a new SystemBreadcrumbBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface $breadcrumb_manager
   *   The breadcrumb manager.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   * @param \Drupal\ui_patterns\UiPatternsManager $patterns_manager
   *   UI Patterns manager.
   * @param \Drupal\ui_patterns\UiPatternsSourceManager $source_manager
   *   UI Patterns source manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, BreadcrumbBuilderInterface $breadcrumb_manager, RouteMatchInterface $route_match, UiPatternsManager $patterns_manager, UiPatternsSourceManager $source_manager, ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $breadcrumb_manager, $route_match);
    $this->patternsManager = $patterns_manager;
    $this->sourceManager = $source_manager;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('breadcrumb'),
      $container->get('current_route_match'),
      $container->get('plugin.manager.ui_patterns'),
      $container->get('plugin.manager.ui_patterns_source'),
      $container->get('module_handler')
    );
  }

  /**
   * Get Source Tag.
   */
  public function getSourceTag() {
    return 'system_breadcrumb_block';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    $config = $this->getConfiguration();

    // Set pattern fields.
    $fields = [];
    $mapping = $config['pattern_mapping'];
    $pattern = $config['pattern'];
    if (!$pattern || $pattern === '_none') {
      return $build;
    }
    if (!array_key_exists('#links', $build)) {
      return $build;
    }
    $mapping = $mapping[$pattern]['settings'];
    foreach ($mapping as $source => $field) {
      if ($field['destination'] === '_hidden') {
        continue;
      }
      if ($source === 'system_breadcrumb_block:links') {
        // See template_preprocess_breadcrumb() in theme.inc.
        foreach ($build['#links'] as $link) {
          $fields[$field['destination']][] = ['text' => $link->getText(), 'url' => $link->getUrl()->toString()];
        };
      }
    }

    $cache = array_key_exists('#cache', $build) ? $build['#cache'] : [];
    $build = $this->buildPattern($fields, $config, $cache);
    return $build;
  }

}
