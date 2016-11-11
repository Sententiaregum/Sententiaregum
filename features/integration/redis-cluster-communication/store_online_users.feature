@redis
Feature: store online users
    In order to see which users are active right now, a redis cluster
    should store their UUIDs

    Scenario: get all active users
        Given the following users are online:
            | uuid                                 |
            | 61335f0e-3901-4c82-b161-5ceb74b7ddc8 |
            | f3085e91-fcd8-4e36-a6ee-13f46dde06aa |
            | fbd0d96d-28f0-4ab2-9ba9-ccab9cd9258b |
        When I check the following UUIDs:
            | uuid                                 |
            | 61335f0e-3901-4c82-b161-5ceb74b7ddc8 |
            | f3085e91-fcd8-4e36-a6ee-13f46dde06aa |
            | fbd0d96d-28f0-4ab2-9ba9-ccab9cd9258b |
            | e2898371-32b6-4918-bdba-32c0594aeffb |
            | 33b7727f-fc03-417d-99f3-c6d0ee9341dc |
        Then I should see the following result:
            | uuid                                 | is_active |
            | 61335f0e-3901-4c82-b161-5ceb74b7ddc8 | true      |
            | f3085e91-fcd8-4e36-a6ee-13f46dde06aa | true      |
            | fbd0d96d-28f0-4ab2-9ba9-ccab9cd9258b | true      |
            | e2898371-32b6-4918-bdba-32c0594aeffb | false     |
            | 33b7727f-fc03-417d-99f3-c6d0ee9341dc | false     |
