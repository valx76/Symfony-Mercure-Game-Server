Feature: Test the ConnectPlayer feature

  Scenario: Connect with no available world
    Given My player name is "user-ok"
    When I connect
    Then I should have a 500 response status code
    And An exception of type "App\Game\Domain\Exception\NoWorldAvailableException" should be thrown

  Scenario: Connect with a player name that is too short
    Given An available world exists
    And My player name is "a"
    When I connect
    Then I should have a 422 response status code
    And An exception of type "Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException" should be thrown

  Scenario: Connect with a player name that is too long
    Given An available world exists
    And My player name is "user-way-too-long-to-be-accepted"
    When I connect
    Then I should have a 422 response status code
    And An exception of type "Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException" should be thrown

  Scenario: Connect
    Given An available world exists
    And My player name is "user-ok"
    When I connect
    Then I should have a 200 response status code
    And I should have a correct ConnectPlayer response
