<?php

namespace Drupal\ui_patterns_blocks\Plugin\UiPatterns\Source;

use Drupal\ui_patterns\Plugin\PatternSourceBase;

/**
 * Defines Field values source plugin.
 *
 * @UiPatternsSource(
 *   id = "system_menu_block",
 *   label = @Translation("System menu block"),
 *   tags = {
 *     "system_menu_block"
 *   }
 * )
 */
class MenuSource extends PatternSourceBase {

  /**
   * {@inheritdoc}
   */
  public function getSourceFields() {
    $sources = [];
    $sources[] = $this->getSourceField('title', 'Title');
    $sources[] = $this->getSourceField('items', 'Items');
    return $sources;
  }

}
