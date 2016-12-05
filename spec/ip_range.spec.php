<?php

describe(\Dxw\CIDR\IPRange::class, function () {
    beforeEach(function () {
        $this->mock = \Mockery::mock('nothing');
        \Dxw\CIDR\IPRange::$IPv6Range = [$this->mock, 'IPv6Range'];
        \Dxw\CIDR\IPRange::$IPv4Range = [$this->mock, 'IPv4Range'];
    });

    describe('::Make()', function () {
        context('with a valid IPv6 range', function () {
            it('returns an IPv6Range', function () {
                $output = 'this in an IPv6Range object';
                $this->mock->shouldReceive('IPv6Range')->with('value')->andReturn(\Dxw\Result\Result::ok($output));

                $result = \Dxw\CIDR\IPRange::Make('value');
                expect($result)->to->be->instanceof(\Dxw\Result\Result::class);
                expect($result->isErr())->to->equal(false);
                expect($result->unwrap())->to->equal($output);
            });
        });

        context('with an invalid IPv6 range', function () {
            context('and a valid IPv4 range', function () {
                it('returns an IPv4Range', function () {
                    $output = 'this in an IPv4Range object';
                    $this->mock->shouldReceive('IPv6Range')->with('value')->andReturn(\Dxw\Result\Result::err('oh no!'));
                    $this->mock->shouldReceive('IPv4Range')->with('value')->andReturn(\Dxw\Result\Result::ok($output));

                    $result = \Dxw\CIDR\IPRange::Make('value');
                    expect($result)->to->be->instanceof(\Dxw\Result\Result::class);
                    expect($result->isErr())->to->equal(false);
                    expect($result->unwrap())->to->equal($output);
                });
            });

            context('and an invalid IPv4 range', function () {
                it('returns an error', function () {
                    $this->mock->shouldReceive('IPv6Range')->with('value')->andReturn(\Dxw\Result\Result::err('oh no!'));
                    $this->mock->shouldReceive('IPv4Range')->with('value')->andReturn(\Dxw\Result\Result::err('oh no again!'));

                    $result = \Dxw\CIDR\IPRange::Make('value');
                    expect($result)->to->be->instanceof(\Dxw\Result\Result::class);
                    expect($result->isErr())->to->equal(true);
                    expect($result->getErr())->to->equal('could not parse range as IPv6 or IPv4');
                });
            });
        });
    });
});
