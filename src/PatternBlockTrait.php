<?php

namespace Drupal\ui_patterns_blocks;

use Drupal\Core\Form\FormStateInterface;

/**
 * Methods shared between blocks plugins.
 *
 * See also: Drupal\ui_patterns\Form\PatternDisplayFormTrait.
 */
trait PatternBlockTrait {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'pattern' => '',
      'variants' => '',
      'pattern_mapping' => [],
      // Used by ui_patterns_settings.
      'pattern_settings' => [],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->configuration;
    // Add UI Patterns form elements.
    $context = [];
    $pattern = $config['pattern'];
    if ($pattern_variant = $this->getCurrentVariant($pattern)) {
      $config['pattern_variant'] = $pattern_variant;
    }
    $this->buildPatternDisplayForm($form, $this->getSourceTag(), $context, $config);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration = $form_state->getValues();
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultValue(array $configuration, $field_name, $value) {
    // Some modifications to make 'destination' default value working.
    $pattern = $configuration['pattern'];
    if (isset($configuration['pattern_mapping'][$pattern]['settings'][$field_name][$value])) {
      return $configuration['pattern_mapping'][$pattern]['settings'][$field_name][$value];
    }
    return NULL;
  }

  /**
   * Checks if a given pattern has a corresponding value on the variants array.
   *
   * @param string $pattern
   *   Pattern ID.
   *
   * @return string|null
   *   Variant ID.
   */
  protected function getCurrentVariant($pattern) {
    $variants = $this->getConfiguration()['variants'];
    return !empty($variants) && isset($variants[$pattern]) ? $variants[$pattern] : NULL;
  }

  /**
   * Build pattern element.
   *
   * @param array $fields
   *   Pattern fields.
   * @param array $config
   *   Block config.
   * @param array $cache
   *   Block cache.
   *
   * @return array
   *   Render array.
   */
  protected function buildPattern(array $fields, array $config, array $cache) {
    $pattern = $config['pattern'];
    $build = [
      '#type' => 'pattern',
      '#id' => $config['pattern'],
      '#fields' => $fields,
      '#cache' => $cache,
    ];

    // Set the variant.
    if ($pattern_variant = $this->getCurrentVariant($pattern)) {
      $build['#variant'] = $pattern_variant;
    }

    // Set the settings.
    $settings = $config['pattern_settings'];
    $pattern_settings = !empty($settings) && isset($settings[$pattern]) ? $settings[$pattern] : NULL;
    if (isset($pattern_settings)) {
      $build['#settings'] = $pattern_settings;
    }
    return $build;
  }

}
