<?php

describe(\Dxw\CIDR\IPv6Block::class, function () {
	describe('::Make()', function () {
		it('creates blocks', function () {
			$result = \Dxw\CIDR\IPv6Block::Make(54);

			expect($result->isErr())->toEqual(false);
			expect($result->unwrap())->toBeAnInstanceOf(\Dxw\CIDR\IPv6Block::class);
			expect($result->unwrap()->getValue())->toEqual(54);
		});

		it('rejects too-large values', function () {
			$result = \Dxw\CIDR\IPv6Block::Make(129);

			expect($result->isErr())->toEqual(true);
			expect($result->getErr())->toEqual('block value too large');
		});

		it('rejects too-small values', function () {
			$result = \Dxw\CIDR\IPv6Block::Make(-1);

			expect($result->isErr())->toEqual(true);
			expect($result->getErr())->toEqual('block value too small');
		});
	});

	describe('->getNetmask()', function () {
		it('returns a binary representation for /32', function () {
			$block = \Dxw\CIDR\IPv6Block::Make(32)->unwrap();

			expect($block->getNetmask())->toBeAnInstanceOf(\phpseclib\Math\BigInteger::class);
			expect($block->getNetmask()->toHex())->toEqual(
				'ffffffff000000000000000000000000'
			);
		});

		it('returns a binary representation for /0', function () {
			$block = \Dxw\CIDR\IPv6Block::Make(0)->unwrap();

			expect($block->getNetmask())->toBeAnInstanceOf(\phpseclib\Math\BigInteger::class);
			expect($block->getNetmask()->toHex())->toEqual(
				''
			);
		});

		it('returns a binary representation for /8', function () {
			$block = \Dxw\CIDR\IPv6Block::Make(8)->unwrap();

			expect($block->getNetmask())->toBeAnInstanceOf(\phpseclib\Math\BigInteger::class);
			expect($block->getNetmask()->toHex())->toEqual(
				'ff000000000000000000000000000000'
			);
		});

		it('returns a binary representation for /9', function () {
			$block = \Dxw\CIDR\IPv6Block::Make(9)->unwrap();

			expect($block->getNetmask())->toBeAnInstanceOf(\phpseclib\Math\BigInteger::class);
			expect($block->getNetmask()->toHex())->toEqual(
				'ff800000000000000000000000000000'
			);
		});
	});

	describe('->__toString()', function () {
		it('returns strings', function () {
			expect(\Dxw\CIDR\IPv6Block::Make(0)->unwrap()->__toString())->toEqual('/0');
			expect(\Dxw\CIDR\IPv6Block::Make(1)->unwrap()->__toString())->toEqual('/1');
			expect(\Dxw\CIDR\IPv6Block::Make(5)->unwrap()->__toString())->toEqual('/5');
			expect(\Dxw\CIDR\IPv6Block::Make(17)->unwrap()->__toString())->toEqual('/17');
			expect(\Dxw\CIDR\IPv6Block::Make(24)->unwrap()->__toString())->toEqual('/24');
			expect(\Dxw\CIDR\IPv6Block::Make(32)->unwrap()->__toString())->toEqual('/32');
			expect(\Dxw\CIDR\IPv6Block::Make(64)->unwrap()->__toString())->toEqual('/64');
			expect(\Dxw\CIDR\IPv6Block::Make(128)->unwrap()->__toString())->toEqual('/128');
		});
	});
});
