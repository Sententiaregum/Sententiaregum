@users
Feature: user repository
  All extended database query operations using doctrine should be
  abstracted in a repository

  Scenario: remove pending activations
    Given the following users exist:
        | user_id | username | password | email                   | activation_date | is_non_activated |
        | 1       | 0        | 123456   | 0@sententiaregum.dev    | -3 hours        | true             |
        | 2       | 1        | 123456   | 1@sententiaregum.dev    | -3 hours        | true             |
        | 3       | foo      | 123456   | foo@sententiaregum.dev  | -1 day          | false            |
    When I try to delete all users with pending activation
    Then one user should still exist
     And two users should be removed

  Scenario: get follower ids
    Given the user fixtures have been applied
     When I ask for a list of follower ids for user "Ma27"
     Then I should get two ids
