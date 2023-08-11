<?php

namespace Dxw\CIDR;

abstract class BlockBase
{
	/** @var int */
	private $value;

	/** @var int */
	protected static $maxSize;

	public static function Make(int $value): \Dxw\Result\Result
	{
		if ($value > static::$maxSize) {
			return \Dxw\Result\Result::err('block value too large');
		}

		if ($value < 0) {
			return \Dxw\Result\Result::err('block value too small');
		}

		return \Dxw\Result\Result::ok(new static($value));
	}

	final private function __construct(int $value)
	{
		$this->value = $value;
	}

	public function getValue(): int
	{
		return $this->value;
	}

	public function getNetmask(): \phpseclib\Math\BigInteger
	{
		$i = $this->value;
		$s = '';
		for ($x = 0; $x < $i; $x++) {
			$s .= '1';
		}
		for ($x = 0; $x < static::$maxSize - $i; $x++) {
			$s .= '0';
		}

		return new \phpseclib\Math\BigInteger($s, 2);
	}

	public function __toString(): string
	{
		return sprintf('/%d', $this->value);
	}
}
