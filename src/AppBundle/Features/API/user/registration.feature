@user @registration
Feature: registration
    As a poster I'd like to create an account in order to share content with other users

    Scenario: create new user account
        When I send a registration request with the following credentials:
            | username       | password | email                             | recaptchaHash | locale |
            | sententiaregum | 123456   | sententiaregum@sententiaregum.dev | hash          | de     |
        Then I should have an account
        And I should have gotten an activation key in order to approve my account
        And I should have an activation email
        When I enter this api key in order to approve the recently created account
        Then I should be able to login

    Scenario: taking a username that is already taken
        When I send a registration request with the following credentials:
            | username | password | email    | recaptchaHash | locale |
            | Ma27     | 72aM     | m@27.org | hash          | en     |
        Then I should see 'The username "Ma27" is already taken!' for property "username"
        And I should see suggestions for my username

    Scenario Outline: account creation with invalid data
        When I send a registration request with the following credentials:
            | username   | password   | email   | recaptchaHash   | locale   |
            | <username> | <password> | <email> | <recaptchaHash> | <locale> |
        Then I should see '<error>' for property "<property>"

        Examples:
          | username | password | email                   | locale | recaptchaHash | error                                                                                               | property |
          | TestUser | 72aM     | foobar                  | en     | hash          | The email is invalid!                                                                               | email    |
          | +=*      | 123456   | foo@example.org         | en     | hash          | A username can contain alphanumeric characters and the special characters dot, underscore and dash! | username |
          | Ma27_2   | 123456   | Ma27@sententiaregum.dev | en     | hash          | Email address "Ma27@sententiaregum.dev" is already in use!                                          | email    |
          | TestUser | 72aM     | foo@example.org         | fr     | hash          | The locale of the new user is invalid!                                                              | locale   |
          | ab       | 72aM     | foo@example.org         | en     | hash          | The username should have at least three characters!                                                 | username |
          | Ma27_2   | 72       | foo@example.org         | en     | hash          | The password should have at least four characters!                                                  | password |
    Scenario: approval with expired activation key
        When I send a registration request with the following credentials:
            | username       | password | email                             | recaptchaHash | locale |
            | sententiaregum | 123456   | sententiaregum@sententiaregum.dev | hash          | de     |
        Then I should have an account
        And I wait more than two hours
        And I try to enter the activation key
        Then the activation should be declined
