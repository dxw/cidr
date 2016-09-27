<?php

namespace Dxw\CIDR;

class IPv6Block
{
    private $value;

    public static function Make(int $value)
    {
        if ($value > 128) {
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

    private function gmp_shiftl($x,$n) {
        return(gmp_mul($x,gmp_pow(2,$n)));
    }

    public function getNetmask(): \GMP
    {
        $i = $this->value;
        $netmask = $this->gmp_shiftl(gmp_sub(gmp_pow(gmp_init(2), $i), gmp_init(1)), 128 - $i);

        return $netmask;
    }
}
