<?php


namespace App\Helpers;


trait QuantityHelper
{
    /**
     * Get single formatted quantity for convert
     * @param $quantity
     * @param $unit_length
     * @return mixed
     */
    protected function formattedSingleQuantity($quantity, $unit_length)
    {
        $input = '';
        for ($i = 0; $i < $unit_length; $i++) {
            if (!array_key_exists($i, $quantity) || $quantity[$i] == null) {
                $quantity[$i] = 0;
            }
            $input .= $quantity[$i];

            if ($unit_length - $i > 1){
                $input .= '/';
            }
        }

        return $input;
    }

    /**
     * get quantity for price type
     * @param $product
     * @param $productQuantity
     * @return float|int
     */
    public static function priceQuantity($product, $productQuantity)
    {
        $unitRelation = explode('/', $product->unit->relation);
        $divided_number = 1;
        for ($i = 0; $i < count($unitRelation); $i++) {
            if ($i >= $product->price_type) {
                $divided_number *= $unitRelation[$i + 1] ?? '1';
            }
        }
        return $productQuantity / $divided_number;
    }

    /**
     * get division number of product quantity
     * @param $unit
     * @param $price_type
     * @return int|string
     */
    public static function getDivisorNumber($unit, $price_type)
    {
        $unitRelation = explode('/', $unit->relation);
        $divisor_number = 1;
        for ($i = 0; $i < count($unitRelation); $i++) {
            if ($i >= $price_type) {
                $divisor_number *= $unitRelation[$i + 1] ?? '1';
            }
        }

        return $divisor_number;
    }
}
