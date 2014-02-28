Feature: rollback
  Rollback code in servers to a previos version
  As a deploy user
  I need to change a symbolic link on all configured remote servers to a previous version of code

  Background:
    Given my system checked and configured

  Scenario: 2 fake servers
    Given a basic deploy with 2 servers
    When I run initialize
    And I run download
    And I run code2production
    And I wait 1 second
    And I run download
    And I run code2production
    And I run rollback
    Then in log I should see "Basic::rollback"
    And I should have a symbolic link to first code version
    And in log I should see subscriber events:
    """
    DeployerTestASubscriber::onRollbackPre
    DeployerTestASubscriber::onRollbackPost
    """