@uuid
Feature: UUID generation with mysql
  Generation of universal unique identifiers with doctrine inside the business logic

  Scenario: create UUID
    Given the database is purged
     When I generate a UUID for a user
      And I persist this user
     Then I should have a valid uuid
      And I should be able to fetch the user

  Scenario: invalid entity manager
    When I try to generate a uuid with wrong entity manager
    Then I should get an error
