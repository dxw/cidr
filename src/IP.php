<?php

namespace Dxw\CIDR;

class IP
{
    // This makes everything easy to test
    public static $IPv6Address = [\Dxw\CIDR\IPv6Address::class, 'Make'];
    public static $IPv6Block = [\Dxw\CIDR\IPv6Block::class, 'Make'];
    public static $IPv6Range = [\Dxw\CIDR\IPv6Range::class, 'Make'];
    public static $IPv4Address = [\Dxw\CIDR\IPv4Address::class, 'Make'];
    public static $IPv4Block = [\Dxw\CIDR\IPv4Block::class, 'Make'];
    public static $IPv4Range = [\Dxw\CIDR\IPv4Range::class, 'Make'];

    public static function contains(string $range, string $address): \Dxw\Result\Result
    {
        $result = call_user_func(self::$IPv6Address, $address);
        if ($result->isErr()) {
            $result = call_user_func(self::$IPv4Address, $address);
        }
        if ($result->isErr()) {
            return $result;
        }
        $_address = $result->unwrap();

        $result = call_user_func(self::$IPv6Range, $range);
        if ($result->isErr()) {
            $result = call_user_func(self::$IPv4Range, $range);
        }
        if ($result->isErr()) {
            return $result;
        }
        $_range = $result->unwrap();

        if ($_address instanceof \Dxw\CIDR\IPv6Address && $_range instanceof \Dxw\CIDR\IPv4Range) {
            return \Dxw\Result\Result::ok(false);
        }
        if ($_address instanceof \Dxw\CIDR\IPv4Address && $_range instanceof \Dxw\CIDR\IPv6Range) {
            return \Dxw\Result\Result::ok(false);
        }

        return \Dxw\Result\Result::ok($_range->containsAddress($_address));
    }
}
