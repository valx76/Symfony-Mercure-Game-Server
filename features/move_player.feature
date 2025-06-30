Feature: Test the MovePlayer feature

  Scenario: Move a player that does not exist
    Given An available world exists
    And I have a player id that does not exist
    When I move to position "3,3"
    Then I should have a 500 response status code
    And An exception of type "App\Game\Domain\Exception\PlayerNotFoundException" should be thrown

  Scenario: Move a player outside of the level
    Given An available world exists
    And I have a player id that exists
    When I move to position "123456,123456"
    Then I should have a 500 response status code
    And An exception of type "App\SharedContext\Domain\Exception\PositionOutOfAreaException" should be thrown

  Scenario: Move a player to a blocking position
    Given An available world exists
    And I have a player id that exists
    When I move to position "1,1"
    Then I should have a 500 response status code
    And An exception of type "App\SharedContext\Domain\Exception\PositionCollidingException" should be thrown

  Scenario: Move
    Given An available world exists
    And I have a player id that exists
    When I move to position "3,3"
    Then I should have a 200 response status code
    And I should have a correct MovePlayer response
    And A notification should be sent to the players on the same world and level
