<?php

namespace App\...;

use App\...\FloatCalculator;

class Example
{
    public function __construct(
        private FloatCalculator $floatCalculator
    ) {
    }

    /**
     * @see https://ru.wikipedia.org/wiki/%D0%A1%D1%80%D0%B5%D0%B4%D0%BD%D0%B5%D0%BA%D0%B2%D0%B0%D0%B4%D1%80%D0%B0%D1%82%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B5_%D0%BE%D1%82%D0%BA%D0%BB%D0%BE%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5
     */
    public function calculateStandardDeviation(array $numbers): float
    {
        // sqrt(((amount - avg)^2 + ...) / (count - 1))

        $avg = $this->calculateMatExpectation($numbers);

        $quantityForDiv = $this->floatCalculator->sub(count($numbers), 1);

        if ($this->floatCalculator->isEqual($quantityForDiv, 0)) {
            return 0.0;
        }

        $result = 0.0;
        foreach ($numbers as $number) {
            $sum = $this->floatCalculator->sub($number, $avg);

            $result += $this->floatCalculator->exponent($sum, 2);
        }

        return $this->floatCalculator->sqrt($this->floatCalculator->div($result, $quantityForDiv));
    }

    public function calculateMatExpectation(array $numbers): float
    {
        if (empty($numbers)) {
            return 0.0;
        }

        $quantity = count($numbers);
        $sumPaid = $this->floatCalculator->sumFromArray($numbers);

        return $this->floatCalculator->div($sumPaid, $quantity);
    }
}
