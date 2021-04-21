<?php

namespace StringCalculator;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

interface MutableArrayInterface
{
    public function map(callable $callback): self;

    public function filter(callable $callback): self;

    public function withMutableCopy(callable $callback): self;

    public function hasAtLeast(int $count, callable $callback): self;

    public function append($item = null): self;

    public function each(callable $callback): self;
}

class FluentArray implements MutableArrayInterface, ArrayAccess, IteratorAggregate, Countable
{
    private array $array;

    public function __construct(array $array = [])
    {
        $this->array = $array;
    }

    //==================================================================
    // ArrayAccess
    //==================================================================

    public function offsetExists($offset): bool
    {
        return isset($offset, $this->array);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->array[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->array[] = $value;
        } else {
            $this->array[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }

    //==================================================================
    // Countable
    //==================================================================

    public function count(): int
    {
        return count($this->array);
    }

    //==================================================================
    // IteratorAggregate
    //==================================================================

    public function getIterator()
    {
        return new ArrayIterator($this->array);
    }

    //==================================================================
    // MutableArrayInterface
    //==================================================================

    public function map(callable $callback): MutableArrayInterface
    {
        $this->array = array_map($callback, $this->array);
        return $this;
    }

    public function filter(callable $callback): MutableArrayInterface
    {
        $this->array = array_filter($this->array, $callback);
        return $this;
    }

    public function withMutableCopy(callable $callback): MutableArrayInterface
    {
        $callback(new FluentArray($this->array));

        return $this;
    }

    public function hasAtLeast(int $count, callable $callback): MutableArrayInterface
    {
        if ($this->count() >= $count) {
            $callback($this);
        }
        return $this;
    }

    public function append($item = null): self
    {
        $this->array[] = null;
        return $this;
    }

    public function each(callable $callback): self
    {
        foreach ($this->array as $item) {
            if (!$callback($item)) {
                break;
            }
        }
        return $this;
    }

    //==================================================================
    // Utility
    //==================================================================

    public function reduce(callable $callback, $initial = null)
    {
        foreach ($this->array as $value) {
            $initial = $callback($initial, $value);
        }
        return $initial;
    }

    public function sum(): int
    {
        return $this->reduce(fn($carry, $value) => $carry + $value, 0);
    }

    public function join(string $separator): string
    {
        return implode($separator, $this->array);
    }

    public function &last() {
        return $this->array[$this->count()-1];
    }

}