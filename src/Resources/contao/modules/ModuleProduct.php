<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace RackRuether;

use Contao\Frontend;
use Contao\Date;
use Contao\Config;
use Contao\PageModel;

/**
 * Provide methods regarding Products.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModuleProduct extends Frontend
{

	/**
	 * Add Products to the indexer
	 *
	 * @param array   $arrPages
	 * @param integer $intRoot
	 * @param boolean $blnIsSitemap
	 *
	 * @return array
	 */
	public function getSearchablePages($arrPages, $intRoot=0, $blnIsSitemap=false)
	{
		$arrRoot = array();

		if ($intRoot > 0)
		{
			$arrRoot = $this->Database->getChildRecords($intRoot, 'tl_page');
		}

		$arrProcessed = array();
		$time = Date::floorToMinute();

		// Get all categories
		$objProduct = ProductCategoryModel::findAll();

		// Walk through each category
		if ($objProduct !== null)
		{
			while ($objProduct->next())
			{
				// Skip Products without target page
				if (!$objProduct->jumpTo)
				{
					continue;
				}

				// Skip Products outside the root nodes
				if (!empty($arrRoot) && !\in_array($objProduct->jumpTo, $arrRoot))
				{
					continue;
				}

				// Get the URL of the jumpTo page
				if (!isset($arrProcessed[$objProduct->jumpTo]))
				{
					$objParent = PageModel::findWithDetails($objProduct->jumpTo);

					// The target page does not exist
					if ($objParent === null)
					{
						continue;
					}

					// The target page has not been published (see #5520)
					if (!$objParent->published || ($objParent->start != '' && $objParent->start > $time) || ($objParent->stop != '' && $objParent->stop <= ($time + 60)))
					{
						continue;
					}

					if ($blnIsSitemap)
					{
						// The target page is protected (see #8416)
						if ($objParent->protected)
						{
							continue;
						}

						// The target page is exempt from the sitemap (see #6418)
						if ($objParent->sitemap == 'map_never')
						{
							continue;
						}
					}

					// Generate the URL
					$arrProcessed[$objProduct->jumpTo] = $objParent->getAbsoluteUrl(Config::get('useAutoItem') ? '/%s' : '/items/%s');
				}

				$strUrl = $arrProcessed[$objProduct->jumpTo];

				// Get the items
				$objItems = ProductModel::findPublishedByPid($objProduct->id);

				if ($objItems !== null)
				{
					while ($objItems->next())
					{
						$arrPages[] = sprintf(preg_replace('/%(?!s)/', '%%', $strUrl), ($objItems->alias ?: $objItems->id));
					}
				}
			}
		}

		return $arrPages;
	}
}

class_alias(ModuleProduct::class, 'ModuleProduct');
