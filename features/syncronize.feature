Feature: syncronize
  Syncronize release directories to remote servers
  As a deploy user
  I need to copy release directories that are not on remote servers

  Background:
    Given my system checked and configured

  Scenario: 2 fake servers
    Given a basic deploy with 2 servers
    And I create 3 fake local releases
    When I run initialize
    And I run syncronize
    Then in log I should see "Basic::syncronize"
    And I should have the 3 fake releases in remote servers
    And in log I should see subscriber events:
    """
    DeployerTestASubscriber::onSyncronizePre
    DeployerTestASubscriber::onSyncronizePost
    """