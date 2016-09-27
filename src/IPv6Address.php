<?php

namespace Dxw\CIDR;

class IPv6Address
{
    private $address;

    public static function Make(string $address): \Dxw\Result\Result
    {
        return \Dxw\Result\Result::ok(new self($address));
    }

    private function __construct(string $address)
    {
        $this->address = $address;
    }

    public function __toString(): string
    {
        return $this->address;
    }

    public function getBinary(): string
    {
        return inet_pton($this->address);
    }
}
