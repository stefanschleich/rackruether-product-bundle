<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */
 
use RackRuether\Product;
use RackRuether\ProductModel;
use RackRuether\ProductCategoryModel;
use RackRuether\ModuleProduct;

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
		'productlist'   => 'RackRuether\ModuleProductList',
		'productreader' => 'RackRuether\ModuleProductReader',
		'productpage'   => 'RackRuether\ModuleProductPage'
	)
));

// Style sheet
if (\defined('TL_MODE') && TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'bundles/rackruetherproduct/product.min.css|static';
}

$GLOBALS['TL_MODELS']['tl_product'] = ProductModel::class;
$GLOBALS['TL_MODELS']['tl_product_category'] = ProductCategoryModel::class;

// Register hooks
$GLOBALS['TL_HOOKS']['getPageIdFromUrl'][] = array(Product::class, 'getPageIdFromUrl');
$GLOBALS['TL_HOOKS']['getSearchablePages'][] = array(ModuleProduct::class, 'getSearchablePages');

// Add permissions
$GLOBALS['TL_PERMISSIONS'][] = 'products';
$GLOBALS['TL_PERMISSIONS'][] = 'productp';
