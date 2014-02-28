Feature: code2production
  Put code to production in an atomic way
  As a deploy user
  I need to change a symbolic link on all configured remote servers

  Background:
    Given my system checked and configured

  Scenario: 2 fake servers
    Given a basic deploy with 2 servers
    When I run initialize
    And I run download
    And I run code2production
    Then in log I should see "Basic::code2production"
    And I should have a symbolic link created in remote servers
    And I should have the new current version saved
    And in log I should see subscriber events:
    """
    DeployerTestASubscriber::onCode2productionPre
    DeployerTestASubscriber::onCode2productionPost
    """

  Scenario: Throws an exception after code2production
    Given a basic deploy with 2 servers
    When I run initialize
    And I run download
    And I run code2production
    And I wait 1 second
    And I run download
    And I run code2production and throws an exception
    Then I should have a symbolic link created in remote servers linked to the first downloaded release
    And I should have the first version saved as current version
    And in log I should see subscriber events:
    """
    DeployerTestASubscriber::onCancelCode2productionPre
    DeployerTestASubscriber::onCancelCode2productionPost
    """
