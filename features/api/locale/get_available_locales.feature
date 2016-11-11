@locale
Feature: get available locales
    In order to ensure that all locales are managed by the backend,
    the API should provide acces to a list of locales.

    Scenario: list of supported locales
        When I submit a request to "GET /api/locale.json"
        Then I should get a response with the 200 status code
        And I should get the following response:
            | de      | en      |
            | Deutsch | English |
