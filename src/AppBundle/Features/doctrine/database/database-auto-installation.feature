@fixtures @database
Feature: database installation
    Command which does a huge database setup

    Scenario: schema appliance
        Given the schema is dropped
        When I apply the schema
        Then I the tool should apply the DDL to the database

    Scenario: migration appliance
        Given the schema is dropped
        When I apply the schema with migrations
        Then the migrations should be applied

    Scenario: production fixture appliance
        Given the database is purged
        When I apply production fixtures
        Then the role and admin fixtures should be applied

    Scenario: fixture appliance
        Given the database is purged
        When I apply all fixtures
        Then the fixtures should be loaded

    Scenario: appended fixtures
        Given the database is not empty
        When I apply all fixtures with --append option
        Then the fixtures should be appended

    Scenario: invalid strategy
        When I apply using an invalid strategy
        Then I should see an error from the installer

    Scenario: data is in sync
        Given the database schema is applied
        When I apply the schema
        Then the process should be skipped

    Scenario: error because of invalid options
        When I apply using the --production-fixtures option and the --append option
        Then the appliance should be skipped
