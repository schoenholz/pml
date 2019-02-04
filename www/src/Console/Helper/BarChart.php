<?php

namespace App\Console\Helper;

class BarChart
{
    /**
     * @var int
     */
    private $max;

    private $width = 30;

    private $delimiterRight = ']';

    private $delimiterLeft = '[';

    private $barChar = '#';

    private $emptyChar = '-';

    public function __construct(int $max)
    {
        $this->max = $max;
    }

    public function draw(int $amount): string
    {
        $str = $this->delimiterLeft;

        if ($this->max == 0) {
            $used = $this->width;
        } else {
            $used = floor(($amount / $this->max) * $this->width);
        }

        $str .= str_repeat($this->barChar, $used);
        $str .= str_repeat($this->emptyChar, $this->width - $used);

        $str .= $this->delimiterRight;

        return $str;
    }
}
