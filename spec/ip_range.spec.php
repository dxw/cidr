<?php

use Kahlan\Plugin\Double;

describe(\Dxw\CIDR\IPRange::class, function () {
	beforeEach(function () {
		$this->mock = Double::instance();
		\Dxw\CIDR\IPRange::$IPv6Range = [$this->mock, 'IPv6Range'];
		\Dxw\CIDR\IPRange::$IPv4Range = [$this->mock, 'IPv4Range'];
	});

	describe('::Make()', function () {
		context('with a valid IPv6 range', function () {
			it('returns an IPv6Range', function () {
				$output = 'this in an IPv6Range object';
				allow($this->mock)->toReceive('IPv6Range')->andReturn(\Dxw\Result\Result::ok($output));

				$result = \Dxw\CIDR\IPRange::Make('value');
				expect($result)->toBeAnInstanceOf(\Dxw\Result\Result::class);
				expect($result->isErr())->toEqual(false);
				expect($result->unwrap())->toEqual($output);
			});
		});

		context('with an invalid IPv6 range', function () {
			context('and a valid IPv4 range', function () {
				it('returns an IPv4Range', function () {
					$output = 'this in an IPv4Range object';
					allow($this->mock)->toReceive('IPv6Range')->andReturn(\Dxw\Result\Result::err('oh no!'));
					allow($this->mock)->toReceive('IPv4Range')->andReturn(\Dxw\Result\Result::ok($output));

					$result = \Dxw\CIDR\IPRange::Make('value');
					expect($result)->toBeAnInstanceOf(\Dxw\Result\Result::class);
					expect($result->isErr())->toEqual(false);
					expect($result->unwrap())->toEqual($output);
				});
			});

			context('and an invalid IPv4 range', function () {
				it('returns an error', function () {
					allow($this->mock)->toReceive('IPv6Range')->andReturn(\Dxw\Result\Result::err('oh no!'));
					allow($this->mock)->toReceive('IPv4Range')->andReturn(\Dxw\Result\Result::err('oh no again!'));

					$result = \Dxw\CIDR\IPRange::Make('value');
					expect($result)->toBeAnInstanceOf(\Dxw\Result\Result::class);
					expect($result->isErr())->toEqual(true);
					expect($result->getErr())->toEqual('could not parse range as IPv6 or IPv4');
				});
			});
		});
	});
});
