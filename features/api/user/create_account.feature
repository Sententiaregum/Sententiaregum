@user
Feature: create account
    As a poster I'd like to create an account in order to share content with other users

    Scenario: create new user account
        Given I have the following payload:
        """
          {
          "username":      "sententiaregum",
          "password":      "123456",
          "email":         "sententiaregum@sententiaregum.dev",
          "recaptchaHash": "hash",
          "locale":        "de"
          }
        """
        When I submit a request to "POST /api/users.json"
        Then I should get a response with the 200 status code
        And I should have an account with name "sententiaregum"
        And I should've gotten an email

    Scenario: approve my account
        Given I created an account with username "sententiaregum"
        And I should have "A5WTBA1JE01A8KFDK7LVLN" as activation key
        When I submit a request to "PATCH /api/users/activate.json?username=sententiaregum&activationKey=A5WTBA1JE01A8KFDK7LVLN"
        Then I should get a response with the 204 status code
        And I should be able to login as "sententiaregum"

    Scenario: taking a username that is already taken
        Given I have the following payload:
        """
          {
          "username":      "Ma27",
          "password":      "72aM",
          "email":         "m@27.org",
          "recaptchaHash": "hash",
          "locale":        "en"
          }
        """
        When I submit a request to "POST /api/users.json"
        Then I should get a response with the 400 status code
        And I should see 'The username "Ma27" is already taken!' for property "username"
        And I should see suggestions for my username

    Scenario Outline: account creation with invalid data
        Given I have the following payload:
        """
          {
          "username":      "<username>",
          "password":      "<password>",
          "email":         "<email>",
          "recaptchaHash": "<recaptchaHash>",
          "locale":        "<locale>"
          }
        """
        When I submit a request to "POST /api/users.json"
        Then I should get a response with the 400 status code
        And I should see '<error>' for property "<property>"

        Examples:
          | username | password | email                   | locale | recaptchaHash | error                                                                                               | property |
          | TestUser | 72aM     | foobar                  | en     | hash          | The email is invalid!                                                                               | email    |
          | +=*      | 123456   | foo@example.org         | en     | hash          | A username can contain alphanumeric characters and the special characters dot, underscore and dash! | username |
          | Ma27_2   | 123456   | Ma27@sententiaregum.dev | en     | hash          | Email address "Ma27@sententiaregum.dev" is already in use!                                          | email    |
          | TestUser | 72aM     | foo@example.org         | fr     | hash          | The locale of the new user is invalid!                                                              | locale   |
          | ab       | 72aM     | foo@example.org         | en     | hash          | The username should have at least three characters!                                                 | username |
          | Ma27_2   | 72       | foo@example.org         | en     | hash          | The password should have at least four characters!                                                  | password |
    Scenario: approval with expired activation key
        Given I created an account with username "sententiaregum"
        And I should have "A5WTBA1JE01A8KFDK7LVLN" as activation key
        And I wait more than two hours
        When I submit a request to "PATCH /api/users/activate.json?username=sententiaregum&activationKey=A5WTBA1JE01A8KFDK7LVLN"
        Then I should get a response with the 403 status code
