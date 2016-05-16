@registration
Feature: username suggestions
    If a username is already in use, a generator should provide new usernames
    being similar to the entered one.

    Background:
        Given the following users exist:
            | username | password | email                  |
            | test-foo | 123456   | test-foo@localhost     |
            | test_foo | 123456   | test-foo2015@localhost |

    Scenario: username suggestions
        When I generate suggestions for "test-foo"
        Then I should see 2 name suggestions
