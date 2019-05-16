<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace RackRuether;

use Contao\Module;
use Contao\BackendTemplate;
use Contao\StringUtil;
use Contao\Config;
use Contao\Environment;
use Contao\PageModel;
use Patchwork\Utf8;

/**
 * Class ModuleProductList
 *
 * @property array $product_categories
 * @property int   $product_readerModule
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModuleProductList extends Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_productlist';

	/**
	 * Target pages
	 * @var array
	 */
	protected $arrTargets = array();

	/**
	 * Display a wildcard in the back end
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['productlist'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->product_categories = StringUtil::deserialize($this->product_categories);

		// Return if there are no categories
		if (empty($this->product_categories) || !\is_array($this->product_categories))
		{
			return '';
		}

		// Show the Product reader if an item has been selected
		if ($this->product_readerModule > 0 && (isset($_GET['items']) || (Config::get('useAutoItem') && isset($_GET['auto_item']))))
		{
			return $this->getFrontendModule($this->product_readerModule, $this->strColumn);
		}

		return parent::generate();
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$objProduct = ProductModel::findPublishedByPids($this->product_categories);

		if ($objProduct === null)
		{
			$this->Template->product = array();

			return;
		}

		$arrProduct = array_fill_keys($this->product_categories, array());

		// Add Products
		while ($objProduct->next())
		{
			$arrTemp = $objProduct->row();
			$arrTemp['title'] = StringUtil::specialchars($objProduct->title, true);
			$arrTemp['href'] = $this->generateProductLink($objProduct);

			/** @var ProductCategoryModel $objPid */
			$objPid = $objProduct->getRelated('pid');

			$arrProduct[$objProduct->pid]['items'][] = $arrTemp;
			$arrProduct[$objProduct->pid]['headline'] = $objPid->headline;
			$arrProduct[$objProduct->pid]['title'] = $objPid->title;
		}

		$arrProduct = array_values(array_filter($arrProduct));

		$cat_count = 0;
		$cat_limit = \count($arrProduct);

		// Add classes
		foreach ($arrProduct as $k=>$v)
		{
			$count = 0;
			$limit = \count($v['items']);

			for ($i=0; $i<$limit; $i++)
			{
				$arrProduct[$k]['items'][$i]['class'] = trim(((++$count == 1) ? ' first' : '') . (($count >= $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'));
			}

			$arrProduct[$k]['class'] = trim(((++$cat_count == 1) ? ' first' : '') . (($cat_count >= $cat_limit) ? ' last' : '') . ((($cat_count % 2) == 0) ? ' odd' : ' even'));
		}

		$this->Template->product = $arrProduct;
	}

	/**
	 * Create links and remember pages that have been processed
	 *
	 * @param ProductModel $objProduct
	 *
	 * @return string
	 *
	 * @throws \Exception
	 */
	protected function generateProductLink($objProduct)
	{
		/** @var ProductCategoryModel $objCategory */
		$objCategory = $objProduct->getRelated('pid');
		$jumpTo = (int) $objCategory->jumpTo;

		// A jumpTo page is not mandatory for Product categories (see #6226) but required for the Product list module
		if ($jumpTo < 1)
		{
			throw new \Exception("Product categories without redirect page cannot be used in a Product list");
		}

		// Get the URL from the jumpTo page of the category
		if (!isset($this->arrTargets[$jumpTo]))
		{
			$this->arrTargets[$jumpTo] = ampersand(Environment::get('request'), true);

			if ($jumpTo > 0 && ($objTarget = PageModel::findByPk($jumpTo)) !== null)
			{
				/** @var PageModel $objTarget */
				$this->arrTargets[$jumpTo] = ampersand($objTarget->getFrontendUrl(Config::get('useAutoItem') ? '/%s' : '/items/%s'));
			}
		}

		return sprintf(preg_replace('/%(?!s)/', '%%', $this->arrTargets[$jumpTo]), ($objProduct->alias ?: $objProduct->id));
	}
}

class_alias(ModuleProductList::class, 'ModuleProductList');
