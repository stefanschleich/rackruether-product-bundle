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
use Contao\Input;
use Contao\System;
use Contao\FilesModel;
use Contao\Date;
use Contao\Environment;
use Contao\Pagemodel;
use Contao\Config;
use Patchwork\Utf8;

/**
 * Class ModuleProductPage
 *
 * @property array $product_categories
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModuleProductPage extends Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_productpage';

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
			$objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['productpage'][0]) . ' ###';
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

		return parent::generate();
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$arrOptions = [];

		if(Input::get('organic')) {
			$arrOptions['organic'] = true;
			$this->Template->organic = true;
		}

		if(Input::get('grid')) {
			$this->Template->grid = true;
		}

		$objProduct = ProductModel::findPublishedByPids($this->product_categories,$arrOptions);

		if ($objProduct === null)
		{
			$this->Template->product = array();

			return;
		}

		/** @var PageModel $objPage */
		global $objPage;

		$arrProducts = array_fill_keys($this->product_categories, array());
		$rootDir = System::getContainer()->getParameter('kernel.project_dir');

		$this->Template->numProducts = \count($objProduct);

		// Add Products
		while ($objProduct->next())
		{
			/** @var ProductModel $objProduct */
			$objTemp = (object) $objProduct->row();

			$objTemp->href = $this->generateProductLink($objProduct);

			// Clean the RTE output
			$objTemp->description = StringUtil::toHtml5($objProduct->description);

			$objTemp->description = StringUtil::encodeEmail($objTemp->description);
			$objTemp->addImage = false;

			// Add an image
			if ($objProduct->addImage && $objProduct->singleSRC != '')
			{
				$objModel = FilesModel::findByUuid($objProduct->singleSRC);

				if ($objModel !== null && is_file($rootDir . '/' . $objModel->path))
				{
					// Do not override the field now that we have a model registry (see #6303)
					$arrProduct = $objProduct->row();
					$arrProduct['singleSRC'] = $objModel->path;
					$strLightboxId = 'lightbox[' . substr(md5('mod_productpage_' . $objProduct->id), 0, 6) . ']'; // see #5810

					$this->addImageToTemplate($objTemp, $arrProduct, null, $strLightboxId, $objModel);
				}
			}

			$objTemp->enclosure = array();

			// Add enclosure
			if ($objProduct->addEnclosure)
			{
				$this->addEnclosuresToTemplate($objTemp, $objProduct->row());
			}

			/** @var UserModel $objAuthor */
			$objAuthor = $objProduct->getRelated('author');
			$objTemp->info = sprintf($GLOBALS['TL_LANG']['MSC']['productCreatedBy'], Date::parse($objPage->dateFormat, $objProduct->tstamp), $objAuthor->name);

			/** @var ProductCategoryModel $objPid */
			$objPid = $objProduct->getRelated('pid');

			// Order by PID
			$arrProducts[$objProduct->pid]['items'][] = $objTemp;
			$arrProducts[$objProduct->pid]['headline'] = $objPid->headline;
			$arrProducts[$objProduct->pid]['title'] = $objPid->title;
		}

		$arrProducts = array_values(array_filter($arrProducts));
		$limit_i = \count($arrProducts) - 1;

		// Add classes first, last, even and odd
		for ($i=0; $i<=$limit_i; $i++)
		{
			$class = (($i == 0) ? 'first ' : '') . (($i == $limit_i) ? 'last ' : '') . (($i%2 == 0) ? 'even' : 'odd');
			$arrProducts[$i]['class'] = trim($class);
			$limit_j = \count($arrProducts[$i]['items']) - 1;

			for ($j=0; $j<=$limit_j; $j++)
			{
				$class = (($j == 0) ? 'first ' : '') . (($j == $limit_j) ? 'last ' : '') . (($j%2 == 0) ? 'even' : 'odd');
				$arrProducts[$i]['items'][$j]->class = trim($class);
			}
		}

		$this->Template->product = $arrProducts;
		$this->Template->request = Environment::get('indexFreeRequest');
		$this->Template->topLink = $GLOBALS['TL_LANG']['MSC']['backToTop'];
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

class_alias(ModuleProductPage::class, 'ModuleProductPage');
