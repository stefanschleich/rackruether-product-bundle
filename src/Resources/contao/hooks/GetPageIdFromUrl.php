<?php

namespace RackRuether\ProductBundle\Hooks;

class GetPageIdFromUrl
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