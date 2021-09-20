<?php

namespace RackRuether;

class Product
{
    public function getPageIdFromUrl($arrFragments)
    {
        //dump($arrFragments);

        if($arrFragments[2] == 'bio') {
            return array( $arrFragments[0], 'organic', 1 );
        }

        return $arrFragments;
    }
}

class_alias(Product::class, 'Product');