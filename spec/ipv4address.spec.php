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
    });

    describe('->getBinary()', function () {
        it('returns a binary representation', function () {
            $address = \Dxw\CIDR\IPv4Address::Make('127.0.0.1')->unwrap();

            expect($address->getBinary())->to->be->instanceof(\GMP::class);
            expect(gmp_strval($address->getBinary(), 16))->to->equal(
                '7f000001'
            );
        });
    });
});
