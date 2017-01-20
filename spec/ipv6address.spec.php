<?php

describe(\Dxw\CIDR\IPv6Address::class, function () {
    describe('::Make()', function () {
        it('creates addresses', function () {
            $result = \Dxw\CIDR\IPv6Address::Make('::1');

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv6Address::class);
            expect($result->unwrap()->__toString())->to->equal('::1');
        });

        it('creates addresses (::)', function () {
            $result = \Dxw\CIDR\IPv6Address::Make('::');

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv6Address::class);
            expect($result->unwrap()->__toString())->to->equal('::');
        });

        it('rejects IPv4 addresses', function () {
            $result = \Dxw\CIDR\IPv6Address::Make('127.0.0.1');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('not an IPv6 address');
        });

        it('rejects nonsense addresses', function () {
            $result = \Dxw\CIDR\IPv6Address::Make('hello:there');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('inet_pton error: unrecognised address');
        });

        // https://tools.ietf.org/html/rfc4291#section-2.5.5
        it('rejects IPv4 addresses within IPv6 unless they are within ::/96 or ::ffff:0:0/96', function () {
            $result = \Dxw\CIDR\IPv6Address::Make('::1234:127.0.0.1');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('illegal embedded IPv4 address');
        });
    });

    describe('->getBinary()', function () {
        it('returns a binary representation', function () {
            $address = \Dxw\CIDR\IPv6Address::Make('::1')->unwrap();

            expect($address->getBinary())->to->be->instanceof(\phpseclib\Math\BigInteger::class);
            expect($address->getBinary()->toHex())->to->equal(
                '01'
            );
        });

        // https://tools.ietf.org/html/rfc4291#section-2.5.5.1
        it('returns a binary representation for IPv4-compatible addresses', function () {
            $address = \Dxw\CIDR\IPv6Address::Make('::127.0.0.1')->unwrap();

            expect($address->getBinary())->to->be->instanceof(\phpseclib\Math\BigInteger::class);
            expect($address->getBinary()->toHex())->to->equal(
                '7f000001'
            );
        });

        // https://tools.ietf.org/html/rfc4291#section-2.5.5.2
        it('returns a binary representation for IPv4-mapped addresses', function () {
            $address = \Dxw\CIDR\IPv6Address::Make('::ffff:127.0.0.1')->unwrap();

            expect($address->getBinary())->to->be->instanceof(\phpseclib\Math\BigInteger::class);
            expect($address->getBinary()->toHex())->to->equal(
                'ffff7f000001'
            );
        });
    });
});
