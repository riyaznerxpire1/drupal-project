<?php

declare(strict_types=1);

namespace Drupal\Tests\rate\FunctionalJavascript;

use Drupal\user\Entity\Role;

/**
 * Tests for the "Number Up / Down" widget.
 */
class NodeRateWidgetNumberUpDownTest extends RateWidgetTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create the rate widget.
    $options = [
      ['value' => 1, 'label' => 'Up'],
      ['value' => -1, 'label' => 'Down'],
    ];
    $results = [
      'result_type' => 'vote_hidden',
      'result_position' => 'below',
    ];
    $this->createRateWidget('number_up_down', 'Number Up / Down', 'numberupdown', $options, ['node.article'], [], [], [], $results);

    // Reset any cache.
    drupal_flush_all_caches();

    // Create temporary role.
    $role_id = $this->randomMachineName();
    $role = Role::create([
      'id' => $role_id,
      'label' => $this->randomString(),
    ]);
    $role->grantPermission('cast rate vote on node of article');
    $role->save();

    $this->user1->addRole($role_id);
    $this->user1->save();
    $this->user2->addRole($role_id);
    $this->user2->save();
    $this->user3->addRole($role_id);
    $this->user3->save();
  }

  /**
   * Tests node rate widget.
   *
   * Votes: +++-
   */
  public function testNodeRateWidget1(): void {
    $page = $this->getSession()->getPage();

    // Login as user1.
    $this->drupalLogin($this->user1);
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $page->find('css', '.vote-result');
    $this->assertEquals('No votes have been submitted yet.', $vote_sum->getText());
    // Voting.
    $page->find('css', 'label.numberupdown-rating-label-up')->click();
    // Reload page.
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $page->find('css', '.vote-result');
    $this->assertEquals('1 votes with an average rating of 1.', $vote_sum->getText());

    // Login as user2.
    $this->drupalLogin($this->user2);
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $page->find('css', '.vote-result');
    $this->assertEquals('1 votes with an average rating of 1.', $vote_sum->getText());
    // Voting.
    $page->find('css', 'label.numberupdown-rating-label-up')->click();
    // Reload page.
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $page->find('css', '.vote-result');
    $this->assertEquals('2 votes with an average rating of 1.', $vote_sum->getText());

    // Login as user3.
    $this->drupalLogin($this->user3);
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $page->find('css', '.vote-result');
    $this->assertEquals('2 votes with an average rating of 1.', $vote_sum->getText());
    // Voting.
    $page->find('css', 'label.numberupdown-rating-label-up')->click();
    drupal_flush_all_caches();
    // Reload page.
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $page->find('css', '.vote-result');
    $this->assertEquals('3 votes with an average rating of 1.', $vote_sum->getText());

    // Login as user1.
    $this->drupalLogin($this->user1);
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $page->find('css', '.vote-result');
    $this->assertEquals('3 votes with an average rating of 1.', $vote_sum->getText());
    // Unvoting.
    $page->find('css', 'label.numberupdown-rating-label-down')->click();
    // Reload page.
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $page->find('css', '.vote-result');
    $this->assertEquals('3 votes with an average rating of 0.4.', $vote_sum->getText());
  }

  /**
   * Tests node rate widget. Only -1.
   *
   * Votes: -
   */
  public function testNodeRateWidget2(): void {
    $page = $this->getSession()->getPage();

    // Login as user1.
    $this->drupalLogin($this->user1);
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $page->find('css', '.vote-result');
    $this->assertEquals('No votes have been submitted yet.', $vote_sum->getText());
    // Voting -1.
    $page->find('css', 'label.numberupdown-rating-label-down')->click();
    // Reload page.
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $page->find('css', '.vote-result');
    $this->assertEquals('1 votes with an average rating of -1.', $vote_sum->getText());
  }

}
