<?php

namespace Drupal\ui_patterns_blocks\Plugin\Block;

use Drupal\system\Plugin\Block\SystemBrandingBlock as CoreSystemBrandingBlock;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ui_patterns\Form\PatternDisplayFormTrait;
use Drupal\ui_patterns\UiPatternsSourceManager;
use Drupal\ui_patterns_blocks\PatternBlockTrait;
use Drupal\ui_patterns\UiPatternsManager;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides a block to display 'Site branding' elements.
 *
 * @Block(
 *   id = "ui_patterns_system_branding_block",
 *   category = @Translation("System (with UI Patterns)"),
 *   admin_label = @Translation("Site branding"),
 *   forms = {
 *     "settings_tray" = "Drupal\system\Form\SystemBrandingOffCanvasForm",
 *   },
 * )
 */
class SystemBrandingBlock extends CoreSystemBrandingBlock {

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
   * Creates a SystemBrandingBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\ui_patterns\UiPatternsManager $patterns_manager
   *   UI Patterns manager.
   * @param \Drupal\ui_patterns\UiPatternsSourceManager $source_manager
   *   UI Patterns source manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, UiPatternsManager $patterns_manager, UiPatternsSourceManager $source_manager, ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $config_factory);
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
      $container->get('config.factory'),
      $container->get('plugin.manager.ui_patterns'),
      $container->get('plugin.manager.ui_patterns_source'),
      $container->get('module_handler')
    );
  }

  /**
   * Get Source Tag.
   */
  public function getSourceTag() {
    return 'system_branding_block';
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
    $mapping = $mapping[$pattern]['settings'];
    foreach ($mapping as $source => $field) {
      if ($field['destination'] === '_hidden') {
        continue;
      }
      // Get rid of the source tag.
      $source = explode(":", $source)[1];
      $fields[$field['destination']] = $build[$source];
    }

    $cache = array_key_exists('#cache', $build) ? $build['#cache'] : [];
    $build = $this->buildPattern($fields, $config, $cache);
    return $build;
  }

}
