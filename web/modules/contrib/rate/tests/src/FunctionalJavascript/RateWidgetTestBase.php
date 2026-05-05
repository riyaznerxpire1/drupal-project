<?php

declare(strict_types=1);

namespace Drupal\Tests\rate\FunctionalJavascript;

use Drupal\comment\Tests\CommentTestTrait;
use Drupal\Core\Session\AccountInterface;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeInterface;
use Drupal\Tests\rate\Traits\NodeVoteTrait;
use Drupal\Tests\rate\Traits\RateWidgetCreateTrait;

/**
 * Base class for Rate Widget tests.
 */
abstract class RateWidgetTestBase extends WebDriverTestBase {

  use RateWidgetCreateTrait;
  use NodeVoteTrait;
  use CommentTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'comment',
    'rate',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Test node.
   */
  protected NodeInterface $node1;

  /**
   * The User used for the test.
   */
  protected AccountInterface $user1;

  /**
   * The User used for the test.
   */
  protected AccountInterface $user2;

  /**
   * The User used for the test.
   */
  protected AccountInterface $user3;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    NodeType::create([
      'type' => 'article',
      'name' => 'Article',
    ])->save();

    $this->addDefaultCommentField('node', 'article');

    $this->user1 = $this->DrupalCreateUser([
      'access content',
      'access comments',
    ]);

    $this->user2 = $this->DrupalCreateUser([
      'access content',
      'access comments',
    ]);

    $this->user3 = $this->DrupalCreateUser([
      'access content',
      'access comments',
    ]);

    $this->node1 = $this->drupalCreateNode([
      'type' => 'article',
      'nid' => 1,
      'uid' => $this->user1->id(),
    ]);
  }

}
