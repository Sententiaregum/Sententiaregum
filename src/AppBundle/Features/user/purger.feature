@users
Feature: activation purger
  Unfinished activations older than two hours will be purged during a purger job

  Background:
    Given the following users exist:
      | user_id | username | password | email                   | activation_date | is_non_activated |
      | 1       | 0        | 123456   | 0@sententiaregum.dev    | -3 hours        | true             |
      | 2       | 1        | 123456   | 1@sententiaregum.dev    | -3 hours        | true             |
      | 3       | foo      | 123456   | foo@sententiaregum.dev  | -1 day          | false            |

  Scenario: purge users
    When I trigger the command to remove all users having a pending activation
    Then All users with a pending and outdated activation should be removed
     And I should see the amount of purged users
