Feature: Managing the cart
    In order to place an order
    As a shop client
    I want to manage the cart

    @domain @application
    Scenario: Picking up a cart
        When I pick up a cart
        Then the cart should be picked up

    @domain @application
    Scenario: Adding cart item to the cart
        Given the cart was picked up
        When I add two "Fallout" cart items to that cart
        Then two "Fallout" cart items should be added to the cart

    @domain @application
    Scenario: Removing cart items from the cart
        Given the cart was picked up
        And two "Fallout" cart items were added to the cart
        When I remove "Fallout" cart item from the cart
        Then the "Fallout" cart item should be removed from the cart

    @domain @application
    Scenario: Adjusting cart item quantity
        Given the cart was picked up
        And two "Fallout" cart items were added to the cart
        When I adjust "Fallout" cart item quantity to five
        Then the "Fallout" cart item quantity should be adjusted to five

    @domain @application
    Scenario: Adjusting cart item quantity to zero removes it instead
        Given the cart was picked up
        And two "Fallout" cart items were added to the cart
        When I adjust "Fallout" cart item quantity to zero
        Then the "Fallout" cart item should be removed from the cart

    @domain @application
    Scenario: Adding the same cart item twice increases its quantity
        Given the cart was picked up
        And two "Fallout" cart items were added to the cart
        When I add three "Fallout" cart items to that cart
        Then the "Fallout" cart item quantity should be adjusted to five
