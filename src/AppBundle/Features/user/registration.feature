@registration
Feature: registration
  As a poster I'd like to create an account in order to share content with other users

  Scenario: create new user account
    When I send a registration request with the following credentials:
      | username       | password | email                             | locale |
      | sententiaregum | 123456   | sententiaregum@sententiaregum.dev | de     |
    Then I should have an account
     And I should have gotten an activation key in order to approve my account
     And I should have an activation email
    When I enter this api key in order to approve the recently created account
    Then I should be able to login

  Scenario: taking a username that is already taken
    When I send a registration request with the following credentials:
      | username | password | email    | locale |
      | Ma27     | 72aM     | m@27.org | en     |
    Then I should see 'The username "Ma27" is already taken!' for property "username"
     And I should see suggestions for my username

  Scenario: taking an email with an invalid email
    When I send a registration request with the following credentials:
      | username | password | email  | locale |
      | TestUser | 72aM     | foobar | en     |
    Then I should see "The email is invalid!" for property "email"

  Scenario: taking an invalid username
    When I send a registration request with the following credentials:
      | username | password | email           | locale |
      | +=*      | 123456   | foo@example.org | en     |
    Then I should see "A username can contain alphanumeric characters and the special characters dot, underscore and dash!" for property "username"
