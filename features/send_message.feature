Feature: Test the SendMessage feature

  Scenario: Send a message from a player that does not exist
    Given An available world exists
    And I have a player id that does not exist
    When I send the message "Hello"
    Then I should have a 422 response status code
    And An exception with violation of type "App\Game\Infrastructure\Validator\PlayerExists" should be thrown

  Scenario: Send a message
    Given An available world exists with id "myworld"
    And I have a player with id "myplayer"
    When I send the message "Hello there!"
    Then I should have a 200 response status code
    And I should have a correct SendMessage response
    And I should have a Mercure update with topic "message_myworld_App\Game\Domain\Model\Entity\Level\Level1" and content
      """
      {"PLAYER":"myplayer","MESSAGE":"Hello there!"}
      """
