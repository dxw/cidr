<?php

namespace Dxw\CIDR;

class IPv6Address extends IPAddress
{
    protected static $unpackSize = '16';

    public static function Make(string $address): \Dxw\Result\Result
    {
        if (strpos($address, ':') === false) {
            return \Dxw\Result\Result::err('not an IPv6 address');
        }

        return parent::Make($address);
    }
}
