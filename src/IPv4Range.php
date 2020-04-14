<?php

namespace Dxw\CIDR;

class IPv4Range extends RangeBase
{
    /** @var IPv4Address */
    private $address;

    /** @var IPv4Block */
    protected $block;

    /** @var string */
    protected static $addressClass = IPv4Address::class;

    public static function Make(string $range): \Dxw\Result\Result
    {
        if (strpos($range, '/') === false) {
            $result = \Dxw\CIDR\IPv4Address::Make($range);
            if ($result->isErr()) {
                return $result->wrap('cannot make range with invalid address');
            }
            $address = $result->unwrap();

            // This cannot produce an error unless we break IPv4Block
            $block = \Dxw\CIDR\IPv4Block::Make(32)->unwrap();
        } else {
            $split = explode('/', $range);
            $_address = $split[0];
            $_block = $split[1];

            // Make sure the block doesn't contain any nonsense
            if ((string)(int)$_block !== $_block) {
                return \Dxw\Result\Result::err('cannot make range with invalid block size');
            }

            $result = \Dxw\CIDR\IPv4Address::Make($_address);
            if ($result->isErr()) {
                return $result->wrap('cannot make range with invalid address');
            }
            $address = $result->unwrap();

            $result = \Dxw\CIDR\IPv4Block::Make((int) $_block);
            if ($result->isErr()) {
                return $result->wrap('cannot make range with invalid block size');
            }
            $block = $result->unwrap();
        }

        return \Dxw\Result\Result::ok(new self($address, $block));
    }

    private function __construct(\Dxw\CIDR\IPv4Address $address, \Dxw\CIDR\IPv4Block $block)
    {
        $this->address = $address;
        $this->block = $block;
    }

    public function getAddress(): \Dxw\CIDR\IPv4Address
    {
        return $this->address;
    }

    public function getBlock(): \Dxw\CIDR\IPv4Block
    {
        return $this->block;
    }

    public function containsAddress(\Dxw\CIDR\AddressBase $address): bool
    {
        if (!($address instanceof \Dxw\CIDR\IPv4Address)) {
            return false;
        }

        $thisAddress = $this->getAddress()->getBinary();
        $netmask = $this->getBlock()->getNetmask();
        $otherAddress = $address->getBinary();

        $thisAddressMasked = $thisAddress->bitwise_and($netmask);
        $otherAddressMasked = $otherAddress->bitwise_and($netmask);

        return $thisAddressMasked->equals($otherAddressMasked);
    }
}
