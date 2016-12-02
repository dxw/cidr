<?php

describe(\Dxw\CIDR\IP::class, function () {
    context('unit tests (with injection)', function () {
        beforeEach(function () {
            $this->mock = \Mockery::mock('nothing');
            \Dxw\CIDR\IP::$IPAddress = [$this->mock, 'IPAddress'];
            \Dxw\CIDR\IP::$IPRange = [$this->mock, 'IPRange'];
        });

        afterEach(function () {
            \Dxw\CIDR\IP::$IPAddress = [\Dxw\CIDR\IPAddress::class, 'Make'];
            \Dxw\CIDR\IP::$IPRange = [\Dxw\CIDR\IPRange::class, 'Make'];
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

    context('integration tests', function () {
        describe('::contains()', function () {
            context('nonsense range', function () {
                it('returns error', function () {
                    $result = \Dxw\CIDR\IP::contains('/', '::');
                    expect($result->isErr())->to->be->true();
                    expect($result->getErr())->to->equal('could not parse range as IPv6 or IPv4');
                });
            });

            context('nonsense address', function () {
                it('returns error', function () {
                    $result = \Dxw\CIDR\IP::contains('::1', '/');
                    expect($result->isErr())->to->be->true();
                    expect($result->getErr())->to->equal('could not parse address as IPv6 or IPv4');
                });
            });

            context('mismatched address types', function () {
                it('returns false', function () {
                    $result = \Dxw\CIDR\IP::contains('::/64', '127.0.0.1');
                    expect($result->isErr())->to->be->false();
                    expect($result->unwrap())->to->be->false();
                });
            });

            context('matching IPv6', function () {
                it('returns true', function () {
                    $result = \Dxw\CIDR\IP::contains('::/64', '::1');
                    expect($result->isErr())->to->be->false();
                    expect($result->unwrap())->to->be->true();
                });
            });

            context('matching IPv4', function () {
                it('returns true', function () {
                    $result = \Dxw\CIDR\IP::contains('127.0.0.0/24', '127.0.0.1');
                    expect($result->isErr())->to->be->false();
                    expect($result->unwrap())->to->be->true();
                });
            });

            context('matching IPv4-mapped IPv6 address', function () {
                $result = \Dxw\CIDR\IP::contains('::ffff:7f00:0001', '::ffff:127.0.0.1');
                expect($result->isErr())->to->be->false();
                expect($result->unwrap())->to->be->true();
            });
        });
    });
});
