<?php

declare(strict_types=1);

namespace Drupal\Tests\rate\FunctionalJavascript;

use Drupal\comment\Entity\Comment;
use Drupal\user\Entity\Role;

/**
 * Tests for the "Fivestar" widget.
 */
class CommentRateWidgetFivestarTest extends RateWidgetTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create the rate widget.
    $options = [
      ['value' => 1, 'label' => 'Star 1'],
      ['value' => 2, 'label' => 'Star 2'],
      ['value' => 3, 'label' => 'Star 3'],
      ['value' => 4, 'label' => 'Star 4'],
      ['value' => 5, 'label' => 'Star 5'],
    ];
    $results = [
      'result_type' => 'vote_hidden',
      'result_position' => 'below',
    ];
    $this->createRateWidget('fivestar', 'Fivestar', 'fivestar', $options, [], ['node.article'], [], [], $results);

    // Reset any cache.
    drupal_flush_all_caches();

    // Create temporary role.
    $role_id = $this->randomMachineName();
    $role = Role::create([
      'id' => $role_id,
      'label' => $this->randomString(),
    ]);
    $role->grantPermission('cast rate vote on comment on node of article');
    $role->save();

    $this->user1->addRole($role_id);
    $this->user1->save();
    $this->user2->addRole($role_id);
    $this->user2->save();
    $this->user3->addRole($role_id);
    $this->user3->save();
  }

  /**
   * Tests voting.
   */
  public function testVoting(): void {
    $comment = Comment::create([
      'entity_type' => 'node',
      'subject' => 'User 1 comment',
      'entity_id' => $this->node1->id(),
      'comment_type' => 'comment',
      'field_name' => 'comment',
      'uid' => $this->user1->id(),
      'status' => 1,
    ]);
    $comment->save();

    $comment = Comment::create([
      'entity_type' => 'node',
      'subject' => 'Admin comment',
      'entity_id' => $this->node1->id(),
      'comment_type' => 'comment',
      'field_name' => 'comment',
      'uid' => $this->rootUser->id(),
      'status' => 1,
    ]);
    $comment->save();

    $comment = Comment::create([
      'entity_type' => 'node',
      'subject' => 'User 3 comment',
      'entity_id' => $this->node1->id(),
      'comment_type' => 'comment',
      'field_name' => 'comment',
      'uid' => $this->user3->id(),
      'status' => 1,
    ]);
    $comment->save();

    // Login as user1.
    $this->drupalLogin($this->user1);
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $this->assertSession()->elementExists('xpath', "//article[@id='comment-2']")->find('css', '.vote-result');
    $this->assertEquals('No votes have been submitted yet.', $vote_sum->getText());
    // Voting.
    $comment_link = $this->assertSession()->elementExists('xpath', "//article[@id='comment-2']")->find('css', 'label.fivestar-rating-label-4');
    $comment_link->click();
    // Reload page.
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $this->assertSession()->elementExists('xpath', "//article[@id='comment-2']")->find('css', '.vote-result');
    $this->assertEquals('1 votes with an average rating of 4.', $vote_sum->getText());

    // Login as user2.
    $this->drupalLogin($this->user2);
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $this->assertSession()->elementExists('xpath', "//article[@id='comment-2']")->find('css', '.vote-result');
    $this->assertEquals('1 votes with an average rating of 4.', $vote_sum->getText());
    // Voting.
    $comment_link = $this->assertSession()->elementExists('xpath', "//article[@id='comment-2']")->find('css', 'label.fivestar-rating-label-3');
    $comment_link->click();
    // Reload page.
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $this->node1->id());
    // Check sum value.
    $vote_sum = $this->assertSession()->elementExists('xpath', "//article[@id='comment-2']")->find('css', '.vote-result');
    $this->assertEquals('2 votes with an average rating of 3.5.', $vote_sum->getText());
  }

}
