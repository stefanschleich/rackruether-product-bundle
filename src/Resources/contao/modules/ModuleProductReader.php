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
use Contao\Config;
use Contao\Input;
use Contao\StringUtil;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\FilesModel;
use Contao\Environment;
use Contao\System;
use Contao\UserModel;
use Contao\Date;
use Contao\Comments;
use Patchwork\Utf8;

/**
 * Class ModuleProductReader
 *
 * @property Comments $Comments
 * @property string   $com_template
 * @property array    $product_categories
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModuleProductReader extends Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_productreader';

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
			$objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['productreader'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && Config::get('useAutoItem') && isset($_GET['auto_item']))
		{
			Input::setGet('items', Input::get('auto_item'));
		}

		// Do not index or cache the page if no Product has been specified
		if (!Input::get('items'))
		{
			/** @var PageModel $objPage */
			global $objPage;

			$objPage->noSearch = 1;
			$objPage->cache = 0;

			return '';
		}

		$this->product_categories = StringUtil::deserialize($this->product_categories);

		// Do not index or cache the page if there are no categories
		if (empty($this->product_categories) || !\is_array($this->product_categories))
		{
			/** @var PageModel $objPage */
			global $objPage;

			$objPage->noSearch = 1;
			$objPage->cache = 0;

			return '';
		}

		return parent::generate();
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		/** @var PageModel $objPage */
		global $objPage;

		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
		$this->Template->referer = 'javascript:history.go(-1)';

		$objProduct = ProductModel::findPublishedByParentAndIdOrAlias(Input::get('items'), $this->product_categories);

		if (null === $objProduct)
		{
			throw new PageNotFoundException('Page not found: ' . Environment::get('uri'));
		}

		// Overwrite the page title and description (see #2853 and #4955)
		if ($objProduct->title != '')
		{
			$objPage->pageTitle = strip_tags(StringUtil::stripInsertTags($objProduct->title));
			$objPage->description = $this->prepareMetaDescription($objProduct->title);
		}

		$this->Template->title = $objProduct->title;

		// Clean the RTE output
		$objProduct->description = StringUtil::toHtml5($objProduct->description);

		$this->Template->description = StringUtil::encodeEmail($objProduct->description);

		// Add details
		$this->Template->orderNumber = $objProduct->orderNumber;
		$this->Template->shopURL = $objProduct->shopURL;
		$this->Template->packingUnit = $objProduct->packingUnit;
		$this->Template->weight = $objProduct->weight;
		$this->Template->allergyFriendly = $objProduct->allergyFriendly;
		$this->Template->organic = $objProduct->organic;
		$this->Template->german = $objProduct->german;
		$this->Template->gmFree = $objProduct->gmFree;

		// Add ingredients
		$this->Template->ingredients = StringUtil::deserialize($objProduct->ingredients);

		// Add nutritionals
		$this->Template->energyKJ = System::getFormattedNumber($objProduct->energyKJ, 2);
		$this->Template->energyKcal = System::getFormattedNumber($objProduct->energyKcal, 2);
		$this->Template->fat = System::getFormattedNumber($objProduct->fat, 2);
		$this->Template->fatAcid = System::getFormattedNumber($objProduct->fatAcid, 2);
		$this->Template->carbs = System::getFormattedNumber($objProduct->carbs, 2);
		$this->Template->carbsSugar = System::getFormattedNumber($objProduct->carbsSugar, 2);
		$this->Template->protein = System::getFormattedNumber($objProduct->protein, 2);
		$this->Template->salt = System::getFormattedNumber($objProduct->salt, 2);

		$this->Template->addImage = false;

		// Add image
		if ($objProduct->addImage && $objProduct->singleSRC != '')
		{
			$objModel = FilesModel::findByUuid($objProduct->singleSRC);

			if ($objModel !== null && is_file(System::getContainer()->getParameter('kernel.project_dir') . '/' . $objModel->path))
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrProduct = $objProduct->row();
				$arrProduct['singleSRC'] = $objModel->path;

				$this->addImageToTemplate($this->Template, $arrProduct, null, null, $objModel);
			}
		}

		$this->Template->enclosure = array();

		// Add enclosure
		if ($objProduct->addEnclosure)
		{
			$this->addEnclosuresToTemplate($this->Template, $objProduct->row());
		}

		$strAuthor = '';

		/** @var UserModel $objAuthor */
		if (($objAuthor = $objProduct->getRelated('author')) instanceof UserModel)
		{
			$strAuthor = $objAuthor->name;
		}

		$this->Template->info = sprintf($GLOBALS['TL_LANG']['MSC']['prpductCreatedBy'], Date::parse($objPage->dateFormat, $objProduct->tstamp), $strAuthor);

		$bundles = System::getContainer()->getParameter('kernel.bundles');

		// HOOK: comments extension required
		if ($objProduct->noComments || !isset($bundles['ContaoCommentsBundle']))
		{
			$this->Template->allowComments = false;

			return;
		}

		/** @var ProductCategoryModel $objCategory */
		$objCategory = $objProduct->getRelated('pid');
		$this->Template->allowComments = $objCategory->allowComments;

		// Comments are not allowed
		if (!$objCategory->allowComments)
		{
			return;
		}

		// Adjust the comments headline level
		$intHl = min((int) str_replace('h', '', $this->hl), 5);
		$this->Template->hlc = 'h' . ($intHl + 1);

		$this->import(Comments::class, 'Comments');
		$arrNotifies = array();

		// Notify the system administrator
		if ($objCategory->notify != 'notify_author')
		{
			$arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
		}

		// Notify the author
		if ($objCategory->notify != 'notify_admin')
		{
			/** @var UserModel $objAuthor */
			if (($objAuthor = $objProduct->getRelated('author')) instanceof UserModel && $objAuthor->email != '')
			{
				$arrNotifies[] = $objAuthor->email;
			}
		}

		$objConfig = new \stdClass();

		$objConfig->perPage = $objCategory->perPage;
		$objConfig->order = $objCategory->sortOrder;
		$objConfig->template = $this->com_template;
		$objConfig->requireLogin = $objCategory->requireLogin;
		$objConfig->disableCaptcha = $objCategory->disableCaptcha;
		$objConfig->bbcode = $objCategory->bbcode;
		$objConfig->moderate = $objCategory->moderate;

		$this->Comments->addCommentsToTemplate($this->Template, $objConfig, 'tl_product', $objProduct->id, $arrNotifies);
	}
}

class_alias(ModuleProductReader::class, 'ModuleProductReader');
