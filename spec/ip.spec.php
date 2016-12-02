<?php

describe(\Dxw\CIDR\IP::class, function () {
    beforeEach(function () {
        $this->mock = \Mockery::mock('nothing');
        \Dxw\CIDR\IP::$IPAddress = [$this->mock, 'IPAddress'];
        \Dxw\CIDR\IP::$IPRange = [$this->mock, 'IPRange'];
    });

    describe('::contains()', function () {
        beforeEach(function () {
            $this->address = 'x';
            $this->range = 'y';

            $this->expectError = function (string $err) {
                $result = \Dxw\CIDR\IP::contains($this->range, $this->address);
                expect($result)->to->be->instanceof(\Dxw\Result\Result::class);
                expect($result->isErr())->to->equal(true);
                expect($result->getErr())->to->equal($err);
            };

            $this->expectOkay = function (bool $output) {
                $result = \Dxw\CIDR\IP::contains($this->range, $this->address);
                expect($result)->to->be->instanceof(\Dxw\Result\Result::class);
                expect($result->isErr())->to->equal(false);
                expect($result->unwrap())->to->equal($output);
            };
        });

        // context address is nonsense
        // context address is valid
        //   context range is nonsense
        //   context range is valid
        //     context range contains address
        //     context range does not contain address

        context('address is nonsense', function () {
            beforeEach(function () {
                $this->mock->shouldReceive('IPAddress')->withArgs([$this->address])->andReturn(
                    \Dxw\Result\Result::err('invalid address')
                );
            });

            it('returns error', function () {
                $this->expectError('invalid address');
            });
        });

        context('address is valid', function () {
            beforeEach(function () {
                $this->ipAddress = \Mockery::mock(\Dxw\CIDR\IPv6Address::class);
                $this->mock->shouldReceive('IPAddress')->withArgs([$this->address])->andReturn(
                    \Dxw\Result\Result::ok($this->ipAddress)
                );
            });

            context('range is nonsense', function () {
                beforeEach(function () {
                    $this->mock->shouldReceive('IPRange')->withArgs([$this->range])->andReturn(
                        \Dxw\Result\Result::err('invalid range')
                    );
                });

                it('returns error', function () {
                    $this->expectError('invalid range');
                });
            });

            context('range is valid', function () {
                beforeEach(function () {
                    $this->ipRange = \Mockery::mock(\Dxw\CIDR\IPv6Range::class);
                    $this->mock->shouldReceive('IPRange')->withArgs([$this->range])->andReturn(
                        \Dxw\Result\Result::ok($this->ipRange)
                    );
                });

                context('range does not contain address', function () {
                    beforeEach(function () {
                        $this->ipRange->shouldReceive('containsAddress')->withArgs([$this->ipAddress])->andReturn(false);
                    });

                    it('returns false', function () {
                        $this->expectOkay(false);
                    });
                });

                context('range contains address', function () {
                    beforeEach(function () {
                        $this->ipRange->shouldReceive('containsAddress')->withArgs([$this->ipAddress])->andReturn(true);
                    });

                    it('returns true', function () {
                        $this->expectOkay(true);
                    });
                });
            });
        });
    });
});
