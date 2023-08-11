<?php

describe(\Dxw\CIDR\IPv6Address::class, function () {
    describe('::Make()', function () {
        it('creates addresses', function () {
            $result = \Dxw\CIDR\IPv6Address::Make('::1');

            expect($result->isErr())->toEqual(false);
            expect($result->unwrap())->toBeAnInstanceOf(\Dxw\CIDR\IPv6Address::class);
            expect($result->unwrap()->__toString())->toEqual('::1');
        });

        it('creates addresses (::)', function () {
            $result = \Dxw\CIDR\IPv6Address::Make('::');

            expect($result->isErr())->toEqual(false);
            expect($result->unwrap())->toBeAnInstanceOf(\Dxw\CIDR\IPv6Address::class);
            expect($result->unwrap()->__toString())->toEqual('::');
        });

        it('rejects IPv4 addresses', function () {
            $result = \Dxw\CIDR\IPv6Address::Make('127.0.0.1');

            expect($result->isErr())->toEqual(true);
            expect($result->getErr())->toEqual('not an IPv6 address');
        });

        it('rejects nonsense addresses', function () {
            $result = \Dxw\CIDR\IPv6Address::Make('hello:there');

            expect($result->isErr())->toEqual(true);
            expect($result->getErr())->toEqual('inet_pton error: unrecognised address');
        });

        // https://tools.ietf.org/html/rfc4291#section-2.5.5
        it('rejects IPv4 addresses within IPv6 unless they are within ::/96 or ::ffff:0:0/96', function () {
            $result = \Dxw\CIDR\IPv6Address::Make('::1234:127.0.0.1');

            expect($result->isErr())->toEqual(true);
            expect($result->getErr())->toEqual('illegal embedded IPv4 address');
        });
    });

    describe('::FromBinary()', function () {
        it('handles correct addresses (small)', function () {
            $result = \Dxw\CIDR\IPv6Address::FromBinary(new \phpseclib\Math\BigInteger(1));

            expect($result->isErr())->toEqual(false);
            expect($result->unwrap())->toBeAnInstanceOf(\Dxw\CIDR\IPv6Address::class);
            expect($result->unwrap()->__toString())->toEqual('::1');
        });

        it('handles correct addresses (large)', function () {
            $result = \Dxw\CIDR\IPv6Address::FromBinary(new \phpseclib\Math\BigInteger('ffffffffffffffffffffffffffffffff', 16));

            expect($result->isErr())->toEqual(false);
            expect($result->unwrap())->toBeAnInstanceOf(\Dxw\CIDR\IPv6Address::class);
            expect($result->unwrap()->__toString())->toEqual('ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff');
        });

        it('handles broken addresses (too large)', function () {
            $result = \Dxw\CIDR\IPv6Address::FromBinary(new \phpseclib\Math\BigInteger('100000000000000000000000000000000', 16));

            expect($result->isErr())->toEqual(true);
            expect($result->getErr())->toEqual('address size cannot exceed 128 bytes');
        });

        it('handles broken addresses (negative)', function () {
            $result = \Dxw\CIDR\IPv6Address::FromBinary(new \phpseclib\Math\BigInteger(-1));

            expect($result->isErr())->toEqual(true);
            expect($result->getErr())->toEqual('address cannot be negative');
        });
    });

    describe('->getBinary()', function () {
        it('returns a binary representation', function () {
            $address = \Dxw\CIDR\IPv6Address::Make('::1')->unwrap();

            expect($address->getBinary())->toBeAnInstanceOf(\phpseclib\Math\BigInteger::class);
            expect($address->getBinary()->toHex())->toEqual(
                '01'
            );
        });

        // https://tools.ietf.org/html/rfc4291#section-2.5.5.1
        it('returns a binary representation for IPv4-compatible addresses', function () {
            $address = \Dxw\CIDR\IPv6Address::Make('::127.0.0.1')->unwrap();

            expect($address->getBinary())->toBeAnInstanceOf(\phpseclib\Math\BigInteger::class);
            expect($address->getBinary()->toHex())->toEqual(
                '7f000001'
            );
        });

        // https://tools.ietf.org/html/rfc4291#section-2.5.5.2
        it('returns a binary representation for IPv4-mapped addresses', function () {
            $address = \Dxw\CIDR\IPv6Address::Make('::ffff:127.0.0.1')->unwrap();

            expect($address->getBinary())->toBeAnInstanceOf(\phpseclib\Math\BigInteger::class);
            expect($address->getBinary()->toHex())->toEqual(
                'ffff7f000001'
            );
        });
    });
});
