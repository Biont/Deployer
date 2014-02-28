Feature: clean
  Clean release directories
  As a deploy user
  I want to left only N number of old releases

  Background:
    Given my system checked and configured

  Scenario: 2 fake servers
    Given a basic deploy with 2 servers
    And I create 15 fake releases
    When I run clean
    Then in log I should see "Basic::clean"
    And I should have only 10 from 15 releases in local server
    And I should have only 10 from 15 releases in remote servers
    And in log I should see subscriber events:
    """
    DeployerTestASubscriber::onCleanPre
    DeployerTestASubscriber::onCleanPost
    """