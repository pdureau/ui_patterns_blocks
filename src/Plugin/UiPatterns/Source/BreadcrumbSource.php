<?php

namespace Drupal\ui_patterns_blocks\Plugin\UiPatterns\Source;

use Drupal\ui_patterns\Plugin\PatternSourceBase;

/**
 * Defines Field values source plugin.
 *
 * @UiPatternsSource(
 *   id = "system_breadcrumb_block",
 *   label = @Translation("System breadcrumb block"),
 *   tags = {
 *     "system_breadcrumb_block"
 *   }
 * )
 */
class BreadcrumbSource extends PatternSourceBase {

  /**
   * {@inheritdoc}
   */
  public function getSourceFields() {
    $sources = [];
    $sources[] = $this->getSourceField('links', 'Links');
    return $sources;
  }

}
