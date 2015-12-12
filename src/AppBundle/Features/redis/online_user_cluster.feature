@online_users
Feature: cluster for online users
  In order to store users a cluster containing all user ids is necessary

  Background:
    Given the following users exist:
        | user_id | username  | password | email                     |
        | 123-abc | Ma27_2    | 123456   | Ma27_2@sententiaregum.dev |
        | foo-2ab | ben       | 123456   | ben@sententiaregum.dev    |
        | bar-baz | test      | 123456   | test@sententiaregum.dev   |
      And the user with id "123-abc" will be marked as online

  Scenario: register online users in a cluster
    When I'd like to know the state of the following user ids:
      | user_id |
      | 123-abc |
      | bar-baz |
    Then I should see the following result:
      | user_id | state |
      | 123-abc | true  |
      | bar-baz | false |
