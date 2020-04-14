<?php

namespace Dxw\CIDR;

abstract class RangeBase
{
    /** @psalm-var class-string */
    protected static $addressClass;

    /** @var BlockBase */
    protected $block;

    abstract public function getAddress();

    abstract public function getBlock();

    public function __toString(): string
    {
        $address = $this->getAddress()->getBinary();
        $netmask = $this->getBlock()->getNetmask();
        $masked = $address->bitwise_and($netmask);

        $result = static::$addressClass::FromBinary($masked);
        // $result->isErr() should never be true since the address is already
        // known to be valid
        if ($result->isErr()) {
            throw new \ErrorException("unexpected error returned from FromBinary() constructor");
        }

        return sprintf('%s%s', $result->unwrap(), $this->block);
    }
}
