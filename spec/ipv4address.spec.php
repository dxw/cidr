<?php

describe(\Dxw\CIDR\IPv4Address::class, function () {
    describe('::Make()', function () {
        it('creates addresses', function () {
            $result = \Dxw\CIDR\IPv4Address::Make('127.0.0.1');

            expect($result->isErr())->to->equal(false);
            expect($result->unwrap())->to->be->instanceof(\Dxw\CIDR\IPv4Address::class);
            expect($result->unwrap()->getAddress())->to->equal('127.0.0.1');
        });
    });
});
