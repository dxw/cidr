<?php

describe(\Dxw\CIDR\IPv6Range::class, function () {
	describe('::Make()', function () {
		it('parses a plain address as a /128 block', function () {
			$result = \Dxw\CIDR\IPv6Range::Make('::1');

			expect($result->isErr())->toEqual(false);
			expect($result->unwrap())->toBeAnInstanceOf(\Dxw\CIDR\IPv6Range::class);

			expect($result->unwrap()->getAddress())->toBeAnInstanceOf(\Dxw\CIDR\IPv6Address::class);
			expect($result->unwrap()->getAddress()->__toString())->toEqual('::1');

			expect($result->unwrap()->getBlock())->toBeAnInstanceOf(\Dxw\CIDR\IPv6Block::class);
			expect($result->unwrap()->getBlock()->getValue())->toEqual(128);
		});

		it('parses proper CIDR notation', function () {
			$result = \Dxw\CIDR\IPv6Range::Make('::1/8');

			expect($result->isErr())->toEqual(false);
			expect($result->unwrap())->toBeAnInstanceOf(\Dxw\CIDR\IPv6Range::class);

			expect($result->unwrap()->getAddress())->toBeAnInstanceOf(\Dxw\CIDR\IPv6Address::class);
			expect($result->unwrap()->getAddress()->__toString())->toEqual('::1');

			expect($result->unwrap()->getBlock())->toBeAnInstanceOf(\Dxw\CIDR\IPv6Block::class);
			expect($result->unwrap()->getBlock()->getValue())->toEqual(8);
		});

		it('handles erroneous address-only ranges', function () {
			$result = \Dxw\CIDR\IPv6Range::Make('foo');

			expect($result->isErr())->toEqual(true);
			expect($result->getErr())->toEqual('cannot make range with invalid address: not an IPv6 address');
		});

		it('handles erroneous address portions', function () {
			$result = \Dxw\CIDR\IPv6Range::Make('foo/8');

			expect($result->isErr())->toEqual(true);
			expect($result->getErr())->toEqual('cannot make range with invalid address: not an IPv6 address');
		});

		it('handles non-int block size portions', function () {
			$result = \Dxw\CIDR\IPv6Range::Make('::1/f');

			expect($result->isErr())->toEqual(true);
			expect($result->getErr())->toEqual('cannot make range with invalid block size');
		});

		it('handles out-of-range block size portions', function () {
			$result = \Dxw\CIDR\IPv6Range::Make('::1/129');

			expect($result->isErr())->toEqual(true);
			expect($result->getErr())->toEqual('cannot make range with invalid block size: block value too large');
		});
	});

	describe('->containsAddress()', function () {
		it('recognises that ::1/8 contains ::1', function () {
			$range = \Dxw\CIDR\IPv6Range::Make('::1/8')->unwrap();
			$address = \Dxw\CIDR\IPv6Address::Make('::1')->unwrap();

			expect($range->containsAddress($address))->toEqual(true);
		});

		it('recognises that ::1/8 contains ::1:1', function () {
			$range = \Dxw\CIDR\IPv6Range::Make('::1/8')->unwrap();
			$address = \Dxw\CIDR\IPv6Address::Make('::1:1')->unwrap();

			expect($range->containsAddress($address))->toEqual(true);
		});

		it('recognises that ::1/8 does not contain 1000::1', function () {
			$range = \Dxw\CIDR\IPv6Range::Make('::1/8')->unwrap();
			$address = \Dxw\CIDR\IPv6Address::Make('1000::1')->unwrap();

			expect($range->containsAddress($address))->toEqual(false);
		});

		it('accepts an IPv4Address', function () {
			$range = \Dxw\CIDR\IPv6Range::Make('::1/8')->unwrap();
			$address = \Dxw\CIDR\IPv4Address::Make('127.0.0.1')->unwrap();

			expect($range->containsAddress($address))->toEqual(false);
		});
	});

	describe('->__toString()', function () {
		it('returns strings', function () {
			expect(\Dxw\CIDR\IPv6Range::Make('2001:db8::123/128')->unwrap()->__toString())->toEqual('2001:db8::123/128');
			expect(\Dxw\CIDR\IPv6Range::Make('2001:db8::123/64')->unwrap()->__toString())->toEqual('2001:db8::/64');
		});
	});
});
