Feature: download
  In order to put code in production
  As a deploy user
  I need to clone code repository and syncronize release directories to all configured servers

  Background:
    Given my system checked and configured

  Scenario: 2 fake servers
    Given a basic deploy with 2 servers
    When I run initialize
    And I run download
    Then in log I should see "Basic::download"
    And I should have code cloned in my local releases directory
    And I should have code copied to my configured servers
    And I have new downloaded version as last downloaded version
    And in log I should see subscriber events:
    """
    DeployerTestASubscriber::onDownloadPre
    DeployerTestASubscriber::onDownloadAdaptCode
    DeployerTestASubscriber::onDownloadPost
    """

  Scenario: Throws an exception after download
    Given a basic deploy with 2 servers
    When I run initialize
    And I create 1 fake releases
    And I run download and throws an exception
    Then I only have the first fake release in local releases directory
    And I only have the first fake release in my fake server
    And I have previous last downloaded version
    And I should have the 1 fake releases in remote servers
    And in log I should see subscriber events:
    """
    DeployerTestASubscriber::onCancelDownloadPre
    DeployerTestASubscriber::onCancelDownloadPost
    """
