<?php

declare(strict_types=1);

namespace Drupal\Tests\rate\Functional;

use Drupal\Core\Session\AccountInterface;
use Drupal\node\Entity\NodeType;
use Drupal\rate\Entity\RateWidget;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\rate\Traits\RateWidgetCreateTrait;

/**
 * Holds set of tools for the rate widget testing.
 */
abstract class RateWidgetTestBase extends BrowserTestBase {

  use RateWidgetCreateTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'comment',
    'views',
    'datetime',
    'rate',
  ];

  /**
   * The node access controller.
   *
   * @var \Drupal\Core\Entity\EntityAccessControlHandlerInterface
   */
  protected $accessController;

  /**
   * An array of nodes.
   *
   * @var \Drupal\node\NodeInterface[]
   */
  protected $nodes;

  /**
   * The User used for the test.
   */
  protected AccountInterface $user1;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    NodeType::create([
      'type' => 'article',
      'name' => 'Article',
    ])->save();

    $this->nodes['article'][1] = $this->drupalCreateNode([
      'type' => 'article',
      'nid' => 1,
    ]);
    $this->nodes['article'][1]->save();

    $this->nodes['article'][2] = $this->drupalCreateNode([
      'type' => 'article',
      'nid' => 2,
    ]);
    $this->nodes['article'][2]->save();

    $this->user1 = $this->DrupalCreateUser([
      'access content',
      'access comments',
      'administer rate',
    ]);
  }

  /**
   * Load a rate widget easily.
   *
   * @param string $id
   *   The id of the rate widget.
   *
   * @return \Drupal\rate\Entity\RateWidget
   *   The rate widget Object.
   */
  protected function loadRateWidget($id) {
    return RateWidget::load($id);
  }

}
