<?php

use Kahlan\Plugin\Double;

describe(\Dxw\CIDR\IPAddress::class, function () {
    beforeEach(function () {
        $this->mock = Double::instance();
        \Dxw\CIDR\IPAddress::$IPv6Address = [$this->mock, 'IPv6Address'];
        \Dxw\CIDR\IPAddress::$IPv4Address = [$this->mock, 'IPv4Address'];
    });

    describe('::Make()', function () {
        context('with a valid IPv6 address', function () {
            it('returns an IPv6Address', function () {
                $output = 'this in an IPv6Address object';
                allow($this->mock)->toReceive('IPv6Address')->andReturn(\Dxw\Result\Result::ok($output));

                $result = \Dxw\CIDR\IPAddress::Make('value');
                expect($result)->toBeAnInstanceOf(\Dxw\Result\Result::class);
                expect($result->isErr())->toEqual(false);
                expect($result->unwrap())->toEqual($output);
            });
        });

        context('with an invalid IPv6 address', function () {
            context('and a valid IPv4 address', function () {
                it('returns an IPv4Address', function () {
                    $output = 'this in an IPv4Address object';
                    allow($this->mock)->toReceive('IPv6Address')->andReturn(\Dxw\Result\Result::err('oh no!'));
                    allow($this->mock)->toReceive('IPv4Address')->andReturn(\Dxw\Result\Result::ok($output));

                    $result = \Dxw\CIDR\IPAddress::Make('value');
                    expect($result)->toBeAnInstanceOf(\Dxw\Result\Result::class);
                    expect($result->isErr())->toEqual(false);
                    expect($result->unwrap())->toEqual($output);
                });
            });

            context('and an invalid IPv4 address', function () {
                it('returns an error', function () {
                    $this->mock->shouldReceive('IPv6Address')->with('value')->andReturn(\Dxw\Result\Result::err('oh no!'));
                    $this->mock->shouldReceive('IPv4Address')->with('value')->andReturn(\Dxw\Result\Result::err('oh no again!'));

                    $result = \Dxw\CIDR\IPAddress::Make('value');
                    expect($result)->toBeAnInstanceOf(\Dxw\Result\Result::class);
                    expect($result->isErr())->toEqual(true);
                    expect($result->getErr())->toEqual('could not parse address as IPv6 or IPv4');
                });
            });
        });
    });
});
