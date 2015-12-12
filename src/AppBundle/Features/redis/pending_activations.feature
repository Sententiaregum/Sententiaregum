@registration
Feature: pending activations cluster
  In order to increase the speed of pending approval requests, a lightweight key checker implementation
  using a redis cluster is necessary

  Background:
    Given the database is purged
      And there's an activation key stored in redis

  Scenario: approving key
     When I try to approve it
     Then the key should be marked as approvable

  Scenario: expired key
    Given the activation key is expired
     When I try to approve it
     Then the key should not be approvable

  Scenario: expired key, but database backup
    Given the key was removed from redis due to server issues
     When I try to approve it
     Then the key should be marked as approvable
