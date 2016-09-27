<?php

namespace Dxw\CIDR;

class IPv4Block
{
    private $value;

    public static function Make(int $value)
    {
        if ($value > 32) {
            return \Dxw\Result\Result::err('block value too large');
        }

        if ($value < 0) {
            return \Dxw\Result\Result::err('block value too small');
        }

        return \Dxw\Result\Result::ok(new self($value));
    }

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getNetmask(): \GMP
    {
        $i = $this->value;
        $netmask = pow(2, $i) - 1 << (32 - $i);

        return gmp_init($netmask);
    }
}
