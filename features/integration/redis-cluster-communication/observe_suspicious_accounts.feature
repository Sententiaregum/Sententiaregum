@redis
Feature: observe suspicious accounts
    In order to avoid leaked accounts, the security system should observe suspicious activity.
    This is achieved by using a redis cluster which stores the data and expires it after a certain time.

    Scenario: store the UUID of an account in the cluster
        Given the account with UUID "a9652d66-529b-11e6-beb8-9e71128cae77" shows suspicious behavior
        When I check if "a9652d66-529b-11e6-beb8-9e71128cae77" is blocked
        Then the block should be confirmed

    Scenario: UUID gets expired after some time
        Given the account with UUID "a9652d66-529b-11e6-beb8-9e71128cae77" shows suspicious behavior
        And I wait more than one minute
        When I check if "a9652d66-529b-11e6-beb8-9e71128cae77" is blocked
        Then the block should not be confirmed
