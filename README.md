# String Calculator in PHP

This is an implementation of [Roy Osherove's String Calculator Kata](https://osherove.com/tdd-kata-1)

It was written under the PHPStorm IDE, using Behat to drive the [features](features).

For those familiar with Behat, the context/step definitions are in the folder [tests](tests) rather 
than the usual `features/bootstrap`, in part to allow the use of an external submodule to hold the
features.

When cloning, either:

-   Clone and recursively pull the submodule(s)

    ```text
    git clone --recurse-submodules <repo>
    ```
    
-   Or, clone, then init and update the submodules

    ```text
    git submodule update --init
    ```

Feel free to use the [submodules feature files](https://github.com/dmeehan1968/StringCalculatorBDD) in your own 
implementation, or just for reference.