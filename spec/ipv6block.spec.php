<?php

describe(\Dxw\CIDR\IPv6Block::class, function () {
    describe('::Make()', function () {
        it('creates blocks', function () {
            $result = \Dxw\CIDR\IPv6Block::Make(54);

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv6Block::class);
            expect($result->unwrap()->getValue())->to->equal(54);
        });

        it('rejects too-large values', function () {
            $result = \Dxw\CIDR\IPv6Block::Make(129);

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('block value too large');
        });

        it('rejects too-small values', function () {
            $result = \Dxw\CIDR\IPv6Block::Make(-1);

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('block value too small');
        });
    });

    describe('->getNetmask()', function () {
        it('returns a binary representation for /32', function () {
            $block = \Dxw\CIDR\IPv6Block::Make(32)->unwrap();

            expect($block->getNetmask())->to->be->instanceof(\phpseclib\Math\BigInteger::class);
            expect($block->getNetmask()->toHex())->to->equal(
                'ffffffff000000000000000000000000'
            );
        });

        it('returns a binary representation for /0', function () {
            $block = \Dxw\CIDR\IPv6Block::Make(0)->unwrap();

            expect($block->getNetmask())->to->be->instanceof(\phpseclib\Math\BigInteger::class);
            expect($block->getNetmask()->toHex())->to->equal(
                ''
            );
        });

        it('returns a binary representation for /8', function () {
            $block = \Dxw\CIDR\IPv6Block::Make(8)->unwrap();

            expect($block->getNetmask())->to->be->instanceof(\phpseclib\Math\BigInteger::class);
            expect($block->getNetmask()->toHex())->to->equal(
                'ff000000000000000000000000000000'
            );
        });

        it('returns a binary representation for /9', function () {
            $block = \Dxw\CIDR\IPv6Block::Make(9)->unwrap();

            expect($block->getNetmask())->to->be->instanceof(\phpseclib\Math\BigInteger::class);
            expect($block->getNetmask()->toHex())->to->equal(
                'ff800000000000000000000000000000'
            );
        });
    });

    describe('->__toString()', function () {
        it('returns strings', function () {
            expect(\Dxw\CIDR\IPv6Block::Make(0)->unwrap()->__toString())->to->equal('/0');
            expect(\Dxw\CIDR\IPv6Block::Make(1)->unwrap()->__toString())->to->equal('/1');
            expect(\Dxw\CIDR\IPv6Block::Make(5)->unwrap()->__toString())->to->equal('/5');
            expect(\Dxw\CIDR\IPv6Block::Make(17)->unwrap()->__toString())->to->equal('/17');
            expect(\Dxw\CIDR\IPv6Block::Make(24)->unwrap()->__toString())->to->equal('/24');
            expect(\Dxw\CIDR\IPv6Block::Make(32)->unwrap()->__toString())->to->equal('/32');
            expect(\Dxw\CIDR\IPv6Block::Make(64)->unwrap()->__toString())->to->equal('/64');
            expect(\Dxw\CIDR\IPv6Block::Make(128)->unwrap()->__toString())->to->equal('/128');
        });
    });
});
