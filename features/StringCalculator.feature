Feature: A String Calculator based on Roy Osherove's Kata

  Scenario: The calculator conforms to the interface
    Given there is a string calculator
    Then it implements the "StringCalculator\StringCalculatorInterface" interface

  Scenario Outline: It can add zero, one or two numbers, separated by comma
    Given there is a string calculator
    When I add "<string>"
    Then the result is <result>

    Examples:
      | string  | result  |
      |         | 0       |
      | 0       | 0       |
      | 1       | 1       |
      | 1,2     | 3       |

  Scenario Outline: It can add any number of numbers
    Given there is a string calculator
    When I add "<string>"
    Then the result is <result>

    Examples:
      | string              | result  |
      | 1,2,3               | 6       |
      | 1,2,3,4,5,6,7,8,9   | 45      |
      | 10,20,30            | 60      |

