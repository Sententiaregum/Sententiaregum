@registration
Feature: registration
  As a poster I'd like to create an account in order to share content with other users

  Scenario: create new user account
    When I send an registration request with the following credentials:
      | username       | password | email                             | locale |
      | sententiaregum | 123456   | sententiaregum@sententiaregum.dev | de     |
    Then I should have an account
     And I should have gotten an activation key in order to approve my account
     And I should have an activation email
    When I enter this api key in order to approve the recently created account
    Then I should be able to login
