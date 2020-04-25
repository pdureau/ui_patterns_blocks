<?php

namespace Drupal\ui_patterns_blocks\Plugin\Block;

use Drupal\system\Plugin\Block\SystemMenuBlock as CoreSystemMenuBlock;
use Drupal\Core\Menu\MenuActiveTrailInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ui_patterns_blocks\PatternBlockTrait;
use Drupal\ui_patterns\Form\PatternDisplayFormTrait;
use Drupal\ui_patterns\UiPatternsSourceManager;
use Drupal\ui_patterns\UiPatternsManager;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides a generic Menu block.
 *
 * @Block(
 *   id = "ui_patterns_system_menu_block",
 *   admin_label = @Translation("Menu"),
 *   category = @Translation("Menus (with UI Patterns)"),
 *   deriver = "Drupal\system\Plugin\Derivative\SystemMenuBlock",
 *   forms = {
 *     "settings_tray" = "\Drupal\system\Form\SystemMenuOffCanvasForm",
 *   },
 * )
 */
class SystemMenuBlock extends CoreSystemMenuBlock {

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
   * Constructs a new SystemMenuBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $menu_tree
   *   The menu tree service.
   * @param \Drupal\ui_patterns\UiPatternsManager $patterns_manager
   *   UI Patterns manager.
   * @param \Drupal\ui_patterns\UiPatternsSourceManager $source_manager
   *   UI Patterns source manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler.
   * @param \Drupal\Core\Menu\MenuActiveTrailInterface $menu_active_trail
   *   The active menu trail service.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, MenuLinkTreeInterface $menu_tree, UiPatternsManager $patterns_manager, UiPatternsSourceManager $source_manager, ModuleHandlerInterface $module_handler, MenuActiveTrailInterface $menu_active_trail = NULL) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $menu_tree, $menu_active_trail);
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
      $container->get('menu.link_tree'),
      $container->get('plugin.manager.ui_patterns'),
      $container->get('plugin.manager.ui_patterns_source'),
      $container->get('module_handler'),
      $container->get('menu.active_trail')
    );
  }

  /**
   * Get Source Tag.
   */
  public function getSourceTag() {
    return 'system_menu_block';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    $config = $this->getConfiguration();

    // Don't display menu if empty.
    if (!array_key_exists('#items', $build)) {
      return $build;
    }

    // Set pattern fields.
    $fields = [];
    $mapping = $config['pattern_mapping'];
    $pattern = $config['pattern'];
    if (!$pattern || $pattern === '_none') {
      return $build;
    }
    $mapping = $mapping[$pattern]['settings'];
    foreach ($mapping as $source => $field) {
      if ($field['destination'] === '_hidden') {
        continue;
      }
      if ($source === 'system_menu_block:title') {
        $fields[$field['destination']] = $this->configuration['label'];
      }
      if ($source === 'system_menu_block:items') {
        $fields[$field['destination']] = $build['#items'];
      }
    }

    $cache = array_key_exists('#cache', $build) ? $build['#cache'] : [];
    $build = $this->buildPattern($fields, $config, $cache);
    return $build;
  }

}
