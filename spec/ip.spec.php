<?php

describe(\Dxw\CIDR\IP::class, function () {
    beforeEach(function () {
        $this->mock = \Mockery::mock('nothing');
        \Dxw\CIDR\IP::$IPv6Address = [$this->mock, 'IPv6Address'];
        \Dxw\CIDR\IP::$IPv6Block = [$this->mock, 'IPv6Block'];
        \Dxw\CIDR\IP::$IPv6Range = [$this->mock, 'IPv6Range'];
        \Dxw\CIDR\IP::$IPv4Address = [$this->mock, 'IPv4Address'];
        \Dxw\CIDR\IP::$IPv4Block = [$this->mock, 'IPv4Block'];
        \Dxw\CIDR\IP::$IPv4Range = [$this->mock, 'IPv4Range'];
    });

    describe('::contains()', function () {
        beforeEach(function () {
            $this->expectError = function (string $err) {
                $result = \Dxw\CIDR\IP::contains('y', 'x');
                expect($result)->to->be->instanceof(\Dxw\Result\Result::class);
                expect($result->isErr())->to->equal(true);
                expect($result->getErr())->to->equal($err);
            };

            $this->expectOkay = function (bool $output) {
                $result = \Dxw\CIDR\IP::contains('y', 'x');
                expect($result)->to->be->instanceof(\Dxw\Result\Result::class);
                expect($result->isErr())->to->equal(false);
                expect($result->unwrap())->to->equal($output);
            };
        });

        // context address is nonsense
        // context address is IPv6
        //   context range is nonsense
        //   context range is IPv4
        //   context range is IPv6
        //     context range contains address
        //     context range does not contain address
        // context address is IPv4
        //   context range is nonsense
        //   context range is IPv6
        //   context range is IPv4
        //     context range contains address
        //     context range does not contain address

        context('address is nonsense', function () {
            beforeEach(function () {
                $this->mock->shouldReceive('IPv6Address')->withArgs(['x'])->andReturn(
                    \Dxw\Result\Result::err('not a v6 address')
                );
                $this->mock->shouldReceive('IPv4Address')->withArgs(['x'])->andReturn(
                    \Dxw\Result\Result::err('not a v4 address')
                );
            });

            it('returns error', function () {
                $this->expectError('not a v4 address');
            });
        });

        context('address is IPv6', function () {
            beforeEach(function () {
                $this->ipv6Address = \Mockery::mock(\Dxw\CIDR\IPv6Address::class);
                $this->mock->shouldReceive('IPv6Address')->withArgs(['x'])->andReturn(
                    \Dxw\Result\Result::ok($this->ipv6Address)
                );
            });

            context('range is nonsense', function () {
                beforeEach(function () {
                    $this->mock->shouldReceive('IPv6Range')->withArgs(['y'])->andReturn(
                        \Dxw\Result\Result::err('not a v6 range')
                    );
                    $this->mock->shouldReceive('IPv4Range')->withArgs(['y'])->andReturn(
                        \Dxw\Result\Result::err('not a v4 range')
                    );
                });

                it('returns error', function () {
                    $this->expectError('not a v4 range');
                });
            });

            context('range is IPv4', function () {
                beforeEach(function () {
                    $this->mock->shouldReceive('IPv6Range')->withArgs(['y'])->andReturn(
                        \Dxw\Result\Result::err('not a v6 range')
                    );
                    $this->ipv4Range = \Mockery::mock(\Dxw\CIDR\IPv4Range::class);
                    $this->mock->shouldReceive('IPv4Range')->withArgs(['y'])->andReturn(
                        \Dxw\Result\Result::ok($this->ipv4Range)
                    );
                });

                it('returns false', function () {
                    $this->expectOkay(false);
                });
            });

            context('range is IPv6', function () {
                beforeEach(function () {
                    $this->ipv6Range = \Mockery::mock(\Dxw\CIDR\IPv6Range::class);
                    $this->mock->shouldReceive('IPv6Range')->withArgs(['y'])->andReturn(
                        \Dxw\Result\Result::ok($this->ipv6Range)
                    );
                });

                context('range contains address', function () {
                    beforeEach(function () {
                        $this->ipv6Range->shouldReceive('containsAddress')->withArgs([$this->ipv6Address])->andReturn(true);
                    });


                    it('returns true', function () {
                        $this->expectOkay(true);
                    });
                });

                context('range does not contain address', function () {
                    beforeEach(function () {
                        $this->ipv6Range->shouldReceive('containsAddress')->withArgs([$this->ipv6Address])->andReturn(false);
                    });

                    it('returns false', function () {
                        $this->expectOkay(false);
                    });
                });
            });
        });

        context('address is IPv4', function () {
            beforeEach(function () {
                $this->ipv6Address = \Mockery::mock(\Dxw\CIDR\IPv6Address::class);
                $this->mock->shouldReceive('IPv6Address')->withArgs(['x'])->andReturn(
                    \Dxw\Result\Result::err('nope')
                );
                $this->ipv4Address = \Mockery::mock(\Dxw\CIDR\IPv4Address::class);
                $this->mock->shouldReceive('IPv4Address')->withArgs(['x'])->andReturn(
                    \Dxw\Result\Result::ok($this->ipv4Address)
                );
            });

            context('range is nonsense', function () {
                beforeEach(function () {
                    $this->mock->shouldReceive('IPv4Range')->withArgs(['y'])->andReturn(
                        \Dxw\Result\Result::err('not a v4 range')
                    );
                    $this->mock->shouldReceive('IPv6Range')->withArgs(['y'])->andReturn(
                        \Dxw\Result\Result::err('not a v6 range')
                    );
                });

                it('returns error', function () {
                    $this->expectError('not a v4 range');
                });
            });

            context('range is IPv6', function () {
                beforeEach(function () {
                    $this->ipv6Range = \Mockery::mock(\Dxw\CIDR\IPv6Range::class);
                    $this->mock->shouldReceive('IPv6Range')->withArgs(['y'])->andReturn(
                        \Dxw\Result\Result::ok($this->ipv6Range)
                    );
                });

                it('returns false', function () {
                    $this->expectOkay(false);
                });
            });

            context('range is IPv4', function () {
                beforeEach(function () {
                    $this->mock->shouldReceive('IPv6Range')->withArgs(['y'])->andReturn(
                        \Dxw\Result\Result::err('not a v6 range')
                    );
                    $this->ipv4Range = \Mockery::mock(\Dxw\CIDR\IPv4Range::class);
                    $this->mock->shouldReceive('IPv4Range')->withArgs(['y'])->andReturn(
                        \Dxw\Result\Result::ok($this->ipv4Range)
                    );
                });

                context('range contains address', function () {
                    beforeEach(function () {
                        $this->ipv4Range->shouldReceive('containsAddress')->withArgs([$this->ipv4Address])->andReturn(true);
                    });


                    it('returns true', function () {
                        $this->expectOkay(true);
                    });
                });

                context('range does not contain address', function () {
                    beforeEach(function () {
                        $this->ipv4Range->shouldReceive('containsAddress')->withArgs([$this->ipv4Address])->andReturn(false);
                    });

                    it('returns false', function () {
                        $this->expectOkay(false);
                    });
                });
            });
        });
    });
});
