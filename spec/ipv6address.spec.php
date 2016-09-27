<?php

describe(\Dxw\CIDR\IPv6Address::class, function () {
    describe('::Make()', function () {
        it('creates addresses', function () {
            $result = \Dxw\CIDR\IPv6Address::Make('::1');

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv6Address::class);
            expect($result->unwrap()->__toString())->to->equal('::1');
        });
    });

    describe('->getBinary()', function () {
        it('returns a binary representation', function () {
            $address = \Dxw\CIDR\IPv6Address::Make('::1')->unwrap();

            expect($address->getBinary())->to->be->instanceof(\GMP::class);
            expect(gmp_strval($address->getBinary(), 16))->to->equal(
                '1'
            );
        });
    });
});
