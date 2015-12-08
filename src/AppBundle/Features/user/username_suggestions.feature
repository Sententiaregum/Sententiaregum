@registration
Feature: username suggestions
  If a username is already in use, a generator should provide new usernames
  being similar to the entered one.

  Background:
    Given the following users exist:
      | user_id | username     | password | email                  |
      |       1 | test-foo     | 123456   | test-foo@localhost     |
      |       2 | test-foo2015 | 123456   | test-foo2015@localhost |

  Scenario: username suggestions
     When I generate suggestions for "test-foo"
     Then I should see the following name suggestions:
      | suggestion |
      | test_foo   |
