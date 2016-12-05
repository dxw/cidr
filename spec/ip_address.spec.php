<?php

describe(\Dxw\CIDR\IPAddress::class, function () {
    beforeEach(function () {
        $this->mock = \Mockery::mock('nothing');
        \Dxw\CIDR\IPAddress::$IPv6Address = [$this->mock, 'IPv6Address'];
        \Dxw\CIDR\IPAddress::$IPv4Address = [$this->mock, 'IPv4Address'];
    });

    describe('::Make()', function () {
        context('with a valid IPv6 address', function () {
            it('returns an IPv6Address', function () {
                $output = 'this in an IPv6Address object';
                $this->mock->shouldReceive('IPv6Address')->with('value')->andReturn(\Dxw\Result\Result::ok($output));

                $result = \Dxw\CIDR\IPAddress::Make('value');
                expect($result)->to->be->instanceof(\Dxw\Result\Result::class);
                expect($result->isErr())->to->equal(false);
                expect($result->unwrap())->to->equal($output);
            });
        });

        context('with an invalid IPv6 address', function () {
            context('and a valid IPv4 address', function () {
                it('returns an IPv4Address', function () {
                    $output = 'this in an IPv4Address object';
                    $this->mock->shouldReceive('IPv6Address')->with('value')->andReturn(\Dxw\Result\Result::err('oh no!'));
                    $this->mock->shouldReceive('IPv4Address')->with('value')->andReturn(\Dxw\Result\Result::ok($output));

                    $result = \Dxw\CIDR\IPAddress::Make('value');
                    expect($result)->to->be->instanceof(\Dxw\Result\Result::class);
                    expect($result->isErr())->to->equal(false);
                    expect($result->unwrap())->to->equal($output);
                });
            });

            context('and an invalid IPv4 address', function () {
                it('returns an error', function () {
                    $this->mock->shouldReceive('IPv6Address')->with('value')->andReturn(\Dxw\Result\Result::err('oh no!'));
                    $this->mock->shouldReceive('IPv4Address')->with('value')->andReturn(\Dxw\Result\Result::err('oh no again!'));

                    $result = \Dxw\CIDR\IPAddress::Make('value');
                    expect($result)->to->be->instanceof(\Dxw\Result\Result::class);
                    expect($result->isErr())->to->equal(true);
                    expect($result->getErr())->to->equal('could not parse address as IPv6 or IPv4');
                });
            });
        });
    });
});
