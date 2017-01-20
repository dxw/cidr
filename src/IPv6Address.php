<?php

namespace Dxw\CIDR;

class IPv6Address extends AddressBase
{
    public static function Make(string $address): \Dxw\Result\Result
    {
        if (strpos($address, ':') === false) {
            return \Dxw\Result\Result::err('not an IPv6 address');
        }

        $result = parent::Make($address);

        if ($result->isErr()) {
            return $result;
        }

        if (strpos($address, '.') === false) {
            return $result;
        }

        // Handle IPv4-mapped addresses first because IPv4-compatible addresses are deprecated

        $ipv4Mapped = \Dxw\CIDR\IPv6Range::Make('::ffff:0:0/96')->unwrap();
        if ($ipv4Mapped->containsAddress($result->unwrap())) {
            return $result;
        }

        $ipv4Compatible = \Dxw\CIDR\IPv6Range::Make('::/96')->unwrap();
        if ($ipv4Compatible->containsAddress($result->unwrap())) {
            return $result;
        }

        return \Dxw\Result\Result::err('illegal embedded IPv4 address');
    }
}
