<?php

describe(\Dxw\CIDR\IPv4Address::class, function () {
    describe('::Make()', function () {
        it('creates addresses', function () {
            $result = \Dxw\CIDR\IPv4Address::Make('127.0.0.1');

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv4Address::class);
            expect($result->unwrap()->__toString())->to->equal('127.0.0.1');
        });

        it('rejects IPv6 addresses', function () {
            $result = \Dxw\CIDR\IPv4Address::Make('::1');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('not an IPv4 address');
        });

        it('rejects nonsense addresses', function () {
            $result = \Dxw\CIDR\IPv4Address::Make('hello.there');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('inet_pton error: unrecognised address');
        });

        it('rejects IPv4-compatible IPv6 addresses', function () {
            $result = \Dxw\CIDR\IPv4Address::Make('::127.0.0.1');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('not an IPv4 address');
        });

        it('rejects IPv4-mapped IPv6 addresses', function () {
            $result = \Dxw\CIDR\IPv4Address::Make('::ffff:127.0.0.1');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('not an IPv4 address');
        });
    });

    describe('::FromBinary()', function () {
        it('handles correct addresses (small)', function () {
            $result = \Dxw\CIDR\IPv4Address::FromBinary(new \phpseclib\Math\BigInteger(1));

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv4Address::class);
            expect($result->unwrap()->__toString())->to->equal('0.0.0.1');
        });

        it('handles correct addresses (large)', function () {
            $result = \Dxw\CIDR\IPv4Address::FromBinary(new \phpseclib\Math\BigInteger('ffffffff', 16));

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv4Address::class);
            expect($result->unwrap()->__toString())->to->equal('255.255.255.255');
        });

        it('handles broken addresses (too large)', function () {
            $result = \Dxw\CIDR\IPv4Address::FromBinary(new \phpseclib\Math\BigInteger('100000000', 16));

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('address size cannot exceed 32 bytes');
        });

        it('handles broken addresses (negative)', function () {
            $result = \Dxw\CIDR\IPv4Address::FromBinary(new \phpseclib\Math\BigInteger(-1));

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('address cannot be negative');
        });
    });

    describe('->getBinary()', function () {
        it('returns a binary representation', function () {
            $address = \Dxw\CIDR\IPv4Address::Make('127.0.0.1')->unwrap();

            expect($address->getBinary())->to->be->instanceof(\phpseclib\Math\BigInteger::class);
            expect($address->getBinary()->toHex())->to->equal(
                '7f000001'
            );
        });
    });
});
