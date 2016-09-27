<?php

describe(\Dxw\CIDR\IPv6Range::class, function () {
    describe('::Make()', function () {
        it('parses a plain address as a /128 block', function () {
            $result = \Dxw\CIDR\IPv6Range::Make('::1');

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv6Range::class);

            expect($result->unwrap()->getAddress())->to->be->instanceof(\Dxw\CIDR\IPv6Address::class);
            expect($result->unwrap()->getAddress()->__toString())->to->equal('::1');

            expect($result->unwrap()->getBlock())->to->be->instanceof(\Dxw\CIDR\IPv6Block::class);
            expect($result->unwrap()->getBlock()->getValue())->to->equal(128);
        });

        it('parses proper CIDR notation', function () {
            $result = \Dxw\CIDR\IPv6Range::Make('::1/8');

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv6Range::class);

            expect($result->unwrap()->getAddress())->to->be->instanceof(\Dxw\CIDR\IPv6Address::class);
            expect($result->unwrap()->getAddress()->__toString())->to->equal('::1');

            expect($result->unwrap()->getBlock())->to->be->instanceof(\Dxw\CIDR\IPv6Block::class);
            expect($result->unwrap()->getBlock()->getValue())->to->equal(8);
        });
    });

    describe('->containsAddress()', function () {
        it('recognises that ::1/8 contains ::1', function () {
            $range = \Dxw\CIDR\IPv6Range::Make('::1/8')->unwrap();
            $address = \Dxw\CIDR\IPv6Address::Make('::1')->unwrap();

            expect($range->containsAddress($address))->to->equal(true);
        });

        it('recognises that ::1/8 contains ::1:1', function () {
            $range = \Dxw\CIDR\IPv6Range::Make('::1/8')->unwrap();
            $address = \Dxw\CIDR\IPv6Address::Make('::1:1')->unwrap();

            expect($range->containsAddress($address))->to->equal(true);
        });

        it('recognises that ::1/8 does not contain 1000::1', function () {
            $range = \Dxw\CIDR\IPv6Range::Make('::1/8')->unwrap();
            $address = \Dxw\CIDR\IPv6Address::Make('1000::1')->unwrap();

            expect($range->containsAddress($address))->to->equal(false);
        });
    });
});
