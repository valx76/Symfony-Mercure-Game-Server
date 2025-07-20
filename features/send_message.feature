Feature: Test the SendMessage feature

  Scenario: Send a message from a player that does not exist
    Given An available world exists
    And I have a player id that does not exist
    When I send the message "Hello"
    Then I should have a 422 response status code
    And An exception with violation of type "App\Game\Infrastructure\Validator\PlayerExists" should be thrown

  Scenario: Send a message
    Given An available world exists
    And I have a player id that exists
    When I send the message "Hello"
    Then I should have a 200 response status code
    And I should have a correct SendMessage response
