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
});
