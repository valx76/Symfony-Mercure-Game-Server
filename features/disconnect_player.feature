Feature: Test the DisconnectPlayer feature

  Scenario: Disconnect a player that does not exist
    Given An available world exists
    And I have a player id that does not exist
    When I disconnect
    Then I should have a 422 response status code
    And An exception with violation of type "App\Game\Infrastructure\Validator\PlayerExists" should be thrown

  Scenario: Disconnect an existing player
    Given An available world exists
    And I have a player id that exists
    When I disconnect
    Then I should have a 200 response status code
    And I should have a correct DisconnectPlayer response
