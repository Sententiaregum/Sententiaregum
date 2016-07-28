@user @blocked_user_cluster
Feature: blocked user cluster
    In order to store information about accounts with suspicious security behavior, a cluster is needed.

    Scenario: store an account UUID and check for it existance
        When the account with UUID "a9652d66-529b-11e6-beb8-9e71128cae77" shows suspicious behavior
        Then the account with UUID "a9652d66-529b-11e6-beb8-9e71128cae77" should be present in the cluster
