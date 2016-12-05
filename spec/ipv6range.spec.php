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

        it('handles erroneous address-only ranges', function () {
            $result = \Dxw\CIDR\IPv6Range::Make('foo');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('cannot make range with invalid address: not an IPv6 address');
        });

        it('handles erroneous address portions', function () {
            $result = \Dxw\CIDR\IPv6Range::Make('foo/8');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('cannot make range with invalid address: not an IPv6 address');
        });

        it('handles non-int block size portions', function () {
            $result = \Dxw\CIDR\IPv6Range::Make('::1/f');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('cannot make range with invalid block size');
        });

        it('handles out-of-range block size portions', function () {
            $result = \Dxw\CIDR\IPv6Range::Make('::1/129');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('cannot make range with invalid block size: block value too large');
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

        it('accepts an IPv4Address', function () {
            $range = \Dxw\CIDR\IPv6Range::Make('::1/8')->unwrap();
            $address = \Dxw\CIDR\IPv4Address::Make('127.0.0.1')->unwrap();

            expect($range->containsAddress($address))->to->equal(false);
        });
    });
});
