<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

// Add back end modules
array_insert($GLOBALS['BE_MOD']['content'], 7, array
(
	'product' => array
	(
		'tables' => array('tl_product_category', 'tl_product')
	)
));

// Front end modules
array_insert($GLOBALS['FE_MOD'], 3, array
(
	'product' => array
	(
		'productlist'   => 'RackRuether\ProductBundle\ModuleProductList',
		'productreader' => 'RackRuether\ProductBundle\ModuleProductReader',
		'productpage'   => 'RackRuether\ProductBundle\ModuleProductPage'
	)
));

// Style sheet
if (\defined('TL_MODE') && TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'bundles/rackruetherproduct/product.min.css|static';
}

$GLOBALS['TL_MODELS']['tl_product'] = 'RackRuether\ProductBundle\ProductModel';
$GLOBALS['TL_MODELS']['tl_product_category'] = 'RackRuether\ProductBundle\ProductCategoryModel';

// Register hooks
$GLOBALS['TL_HOOKS']['getPageIdFromUrl'][] = array('RackRuether\ProductBundle\Hooks\GetPageIdFromUrl', 'getPageIdFromUrl');
$GLOBALS['TL_HOOKS']['getSearchablePages'][] = array('RackRuether\ProductBundle\ModuleProduct', 'getSearchablePages');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('rackruether_product.listener.insert_tags', 'onReplaceInsertTags');

// Add permissions
$GLOBALS['TL_PERMISSIONS'][] = 'products';
$GLOBALS['TL_PERMISSIONS'][] = 'productp';
