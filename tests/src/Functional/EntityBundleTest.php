<?php

namespace Drupal\Tests\islandora\Functional;

/**
 * Tests the IslandoraEntityBundle condition.
 *
 * @group islandora
 */
class IslandoraEntityBundleTest extends IslandoraFunctionalTestBase {

  /**
   * @covers \Drupal\islandora\Plugin\Condition\IslandoraEntityBundle::buildConfigurationForm
   * @covers \Drupal\islandora\Plugin\Condition\IslandoraEntityBundle::submitConfigurationForm
   * @covers \Drupal\islandora\Plugin\Condition\IslandoraEntityBundle::evaluate
   */
  public function testIslandoraEntityBundleType() {
    // Create a test user.
    $account = $this->drupalCreateUser([
      'bypass node access',
      'administer contexts',
      'administer taxonomy',
    ]);
    $this->drupalLogin($account);

    $this->createContext('Test', 'test');
    $this->addCondition('test', 'islandora_entity_bundle');
    $this->getSession()->getPage()->checkField("edit-conditions-islandora-entity-bundle-bundles-test-type");
    $this->getSession()->getPage()->findById("edit-conditions-islandora-entity-bundle-context-mapping-node")->selectOption("@node.node_route_context:node");
    $this->getSession()->getPage()->pressButton(t('Save and continue'));
    $this->addPresetReaction('test', 'index', 'hello_world');

    // Create a new test_type confirm Hello World! is printed to the screen.
    $this->postNodeAddForm('test_type', ['title[0][value]' => 'Test Node'], 'Save');
    $this->assertSession()->pageTextContains("Hello World!");

    // Create a new term and confirm Hellow World! is NOT printed to the screen.
    $this->postTermAddForm('test_vocabulary', ['name[0][value]' => 'Test Term'], 'Save');
    $this->assertSession()->pageTextNotContains("Hello World!");

  }

}
