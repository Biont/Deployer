Feature: initialize
  In order to prepare deployer to deploy code
  As a deploy user
  I need to prepare a directory structure in local server and remote servers and clone vcs proxy repository

  Background:
    Given my system checked and configured

  Scenario: 2 fake servers
    Given a basic deploy with 2 servers
    When I run initialize
    Then in log I should see "Basic::initialize"
    And I should have a local directory structure
    And I should have a remote directory structure
    And I should have VCS proxy repository cloned
    And in log I should see subscriber events:
    """
    DeployerTestASubscriber::onInitializePre
    DeployerTestASubscriber::onInitializePost
    """

  Scenario: 1 fake server that not exists
    Given a basic deploy with 0 fake server and 1 failing server
    When I run initialize and throw an exception