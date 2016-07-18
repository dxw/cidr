<?php

namespace Dxw\CIDR;

class IPv4Address
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

    public function getAddress(): string
    {
        return $this->address;
    }
}
