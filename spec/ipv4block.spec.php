<?php

describe(\Dxw\CIDR\IPv4Block::class, function () {
    describe('::Make()', function () {
        it('creates blocks', function () {
            $result = \Dxw\CIDR\IPv4Block::Make(8);

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv4Block::class);
            expect($result->unwrap()->getValue())->to->equal(8);
        });

        it('rejects too-large values', function () {
            $result = \Dxw\CIDR\IPv4Block::Make(33);

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('block value too large');
        });

        it('rejects too-small values', function () {
            $result = \Dxw\CIDR\IPv4Block::Make(-1);

            expect($result->isErr())->to->equal(true);
            expect($result->getErr())->to->equal('block value too small');
        });
    });

    describe('->getNetmask()', function () {
        it('returns a binary representation for /32', function () {
            $block = \Dxw\CIDR\IPv4Block::Make(32)->unwrap();

            expect($block->getNetmask())->to->be->instanceof(\phpseclib\Math\BigInteger::class);
            expect($block->getNetmask()->toHex())->to->equal(
                'ffffffff'
            );
        });

        it('returns a binary representation for /0', function () {
            $block = \Dxw\CIDR\IPv4Block::Make(0)->unwrap();

            expect($block->getNetmask())->to->be->instanceof(\phpseclib\Math\BigInteger::class);
            expect($block->getNetmask()->toHex())->to->equal(
                ''
            );
        });

        it('returns a binary representation for /8', function () {
            $block = \Dxw\CIDR\IPv4Block::Make(8)->unwrap();

            expect($block->getNetmask())->to->be->instanceof(\phpseclib\Math\BigInteger::class);
            expect($block->getNetmask()->toHex())->to->equal(
                'ff000000'
            );
        });

        it('returns a binary representation for /9', function () {
            $block = \Dxw\CIDR\IPv4Block::Make(9)->unwrap();

            expect($block->getNetmask())->to->be->instanceof(\phpseclib\Math\BigInteger::class);
            expect($block->getNetmask()->toHex())->to->equal(
                'ff800000'
            );
        });
    });
});
