<?php

describe(\Dxw\CIDR\IPv4Range::class, function () {
    describe('::Make()', function () {
        it('parses a plain address as a /32 block', function () {
            $result = \Dxw\CIDR\IPv4Range::Make('127.0.0.1');

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv4Range::class);

            expect($result->unwrap()->getAddress())->to->be->instanceof(\Dxw\CIDR\IPv4Address::class);
            expect($result->unwrap()->getAddress()->__toString())->to->equal('127.0.0.1');

            expect($result->unwrap()->getBlock())->to->be->instanceof(\Dxw\CIDR\IPv4Block::class);
            expect($result->unwrap()->getBlock()->getValue())->to->equal(32);
        });

        it('parses proper CIDR notation', function () {
            $result = \Dxw\CIDR\IPv4Range::Make('127.0.0.1/8');

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv4Range::class);

            expect($result->unwrap()->getAddress())->to->be->instanceof(\Dxw\CIDR\IPv4Address::class);
            expect($result->unwrap()->getAddress()->__toString())->to->equal('127.0.0.1');

            expect($result->unwrap()->getBlock())->to->be->instanceof(\Dxw\CIDR\IPv4Block::class);
            expect($result->unwrap()->getBlock()->getValue())->to->equal(8);
        });

        it('handles erroneous address-only ranges', function () {
            $result = \Dxw\CIDR\IPv4Range::Make('foo');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('cannot make range with invalid address: not an IPv4 address');
        });

        it('handles erroneous address portions', function () {
            $result = \Dxw\CIDR\IPv4Range::Make('foo/8');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('cannot make range with invalid address: not an IPv4 address');
        });

        it('handles non-int block size portions', function () {
            $result = \Dxw\CIDR\IPv4Range::Make('127.0.0.1/f');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('cannot make range with invalid block size');
        });

        it('handles out-of-range block size portions', function () {
            $result = \Dxw\CIDR\IPv4Range::Make('127.0.0.1/33');

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('cannot make range with invalid block size: block value too large');
        });
    });

    describe('->containsAddress()', function () {
        it('recognises that 127.0.0.1/8 contains 127.0.0.1', function () {
            $range = \Dxw\CIDR\IPv4Range::Make('127.0.0.1/8')->unwrap();
            $address = \Dxw\CIDR\IPv4Address::Make('127.0.0.1')->unwrap();

            expect($range->containsAddress($address))->to->equal(true);
        });

        it('recognises that 127.0.0.1/8 contains 127.255.255.255', function () {
            $range = \Dxw\CIDR\IPv4Range::Make('127.0.0.1/8')->unwrap();
            $address = \Dxw\CIDR\IPv4Address::Make('127.255.255.255')->unwrap();

            expect($range->containsAddress($address))->to->equal(true);
        });

        it('recognises that 127.0.0.1/8 does not contain 128.0.0.1', function () {
            $range = \Dxw\CIDR\IPv4Range::Make('127.0.0.1/8')->unwrap();
            $address = \Dxw\CIDR\IPv4Address::Make('128.255.255.255')->unwrap();

            expect($range->containsAddress($address))->to->equal(false);
        });

        it('accepts IPv6 addresses', function () {
            $range = \Dxw\CIDR\IPv4Range::Make('127.0.0.1/8')->unwrap();
            $address = \Dxw\CIDR\IPv6Address::Make('::7f00:1')->unwrap();

            expect($range->containsAddress($address))->to->equal(false);
        });
    });

    describe('->__toString()', function () {
        it('returns strings', function () {
            expect(\Dxw\CIDR\IPv4Range::Make('192.168.1.1/24')->unwrap()->__toString())->to->equal('192.168.1.0/24');
            expect(\Dxw\CIDR\IPv4Range::Make('127.0.0.1/32')->unwrap()->__toString())->to->equal('127.0.0.1/32');
        });
    });
});
