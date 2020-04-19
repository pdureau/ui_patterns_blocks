<?php

namespace Drupal\ui_patterns_blocks\Plugin\UiPatterns\Source;

use Drupal\ui_patterns\Plugin\PatternSourceBase;

/**
 * Defines Field values source plugin.
 *
 * @UiPatternsSource(
 *   id = "system_branding_block",
 *   label = @Translation("System branding block"),
 *   tags = {
 *     "system_branding_block"
 *   }
 * )
 */
class BrandingSource extends PatternSourceBase {

  /**
   * {@inheritdoc}
   */
  public function getSourceFields() {
    $sources = [];
    $sources[] = $this->getSourceField('site_logo', 'Logo');
    $sources[] = $this->getSourceField('site_name', 'Name');
    $sources[] = $this->getSourceField('site_slogan', 'Slogan');
    return $sources;
  }

}
