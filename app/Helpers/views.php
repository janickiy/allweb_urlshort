<?php

/**
 * Calculate the growth between two values
 *
 * @param $current
 * @param $previous
 * @return array|int
 */
function calcGrowth(int|float|null $current, int|float|null $previous): int|float
{
    if ($previous == 0 || $previous == null || $current == 0) {
        return 0;
    }

    return $result = (($current - $previous) / $previous * 100);
}
