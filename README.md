# UI Patterns Block

## INTRODUCTION

Render core and sytem blocks with UI Patterns.

Aready implemented:

- SystemBrandingBlock
- SystemBreadcrumbBlock
- SystemMenuBlock, using the Drupal\system\Plugin\Derivative\SystemMenuBlock
  deriver.

Will not be implemented:

- SystemMessagesBlock: because all the work is node in StatusMessages render
  element. A presenter template may be better suited.

## REQUIREMENTS

This module requires the following module:

- UI Patterns (https://www.drupal.org/project/ui_patterns):
  Define and expose self-contained UI patterns as Drupal plugins and use them
  seamlessly while site-building.

## INSTALLATION

- Install as you would normally install a contributed Drupal module. Visit
  https://www.drupal.org/node/1897420 for further information.

## CONFIGURATION

The module has no menu or modifiable settings.
