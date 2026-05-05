<?php

declare(strict_types=1);

namespace Drupal\Tests\rate\Functional;

/**
 * Testing the listing functionality for the Rate widget entity.
 */
class RateWidgetListTest extends RateWidgetTestBase {

  /**
   * Listing of rate widgets.
   */
  public function testEntityTypeList() {
    $this->drupalLogin($this->user1);

    // The rate widget settings.
    $options = [];
    $entity_types = ['node.article'];
    $comment_types = [];
    $voting = ['use_deadline' => 0];
    $display = [];
    $results = [];
    // Create the rate widget.
    $this->createRateWidget('dummy_rate_widget', 'Dummy rate widget', 'fivestar', $options, $entity_types, $comment_types, $voting, $display, $results);

    $this->drupalGet('admin/structure/rate_widgets');
    $this->assertSession()->pageTextContains('dummy_rate_widget');
    $this->assertSession()->statusCodeEquals(200);
  }

}
