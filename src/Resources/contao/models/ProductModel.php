<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace RackRuether\ProductBundle;

use Contao\Model\Collection;

/**
 * Reads and writes Products
 *
 * @property integer $id
 * @property integer $pid
 * @property integer $sorting
 * @property integer $tstamp
 * @property string  $title
 * @property string  $alias
 * @property integer $author
 * @property string  $description
 * @property boolean $addImage
 * @property string  $singleSRC
 * @property string  $alt
 * @property string  $size
 * @property string  $imagemargin
 * @property string  $imageUrl
 * @property boolean $fullsize
 * @property string  $caption
 * @property string  $floating
 * @property boolean $addEnclosure
 * @property string  $enclosure
 * @property boolean $noComments
 * @property boolean $published
 *
 * @method static ProductModel|null findById($id, $opt=array())
 * @method static ProductModel|null findByPk($id, array $opt=array())
 * @method static ProductModel|null findByIdOrAlias($val, array $opt=array())
 * @method static ProductModel|null findOneBy($col, $val, array $opt=array())
 * @method static ProductModel|null findOneByPid($val, $opt=array())
 * @method static ProductModel|null findOneBySorting($val, $opt=array())
 * @method static ProductModel|null findOneByTstamp($val, $opt=array())
 * @method static ProductModel|null findOneByTitle($val, $opt=array())
 * @method static ProductModel|null findOneByAlias($val, $opt=array())
 * @method static ProductModel|null findOneByAuthor($val, $opt=array())
 * @method static ProductModel|null findOneByDescription($val, $opt=array())
 * @method static ProductModel|null findOneByAddImage($val, $opt=array())
 * @method static ProductModel|null findOneBySingleSRC($val, $opt=array())
 * @method static ProductModel|null findOneByAlt($val, $opt=array())
 * @method static ProductModel|null findOneBySize($val, $opt=array())
 * @method static ProductModel|null findOneByImagemargin($val, $opt=array())
 * @method static ProductModel|null findOneByImageUrl($val, $opt=array())
 * @method static ProductModel|null findOneByFullsize($val, $opt=array())
 * @method static ProductModel|null findOneByCaption($val, $opt=array())
 * @method static ProductModel|null findOneByFloating($val, $opt=array())
 * @method static ProductModel|null findOneByAddEnclosure($val, $opt=array())
 * @method static ProductModel|null findOneByEnclosure($val, $opt=array())
 * @method static ProductModel|null findOneByNoComments($val, $opt=array())
 * @method static ProductModel|null findOneByPublished($val, $opt=array())
 *
 * @method static Collection|ProductModel[]|ProductModel|null findByPid($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findBySorting($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByTstamp($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByTitle($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByAlias($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByAuthor($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByDescription($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByAddImage($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findBySingleSRC($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByAlt($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findBySize($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByImagemargin($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByImageUrl($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByFullsize($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByCaption($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByFloating($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByAddEnclosure($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByEnclosure($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByNoComments($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findByPublished($val, $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findMultipleByIds($val, array $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findBy($col, $val, array $opt=array())
 * @method static Collection|ProductModel[]|ProductModel|null findAll(array $opt=array())
 *
 * @method static integer countById($id, $opt=array())
 * @method static integer countByPid($val, $opt=array())
 * @method static integer countBySorting($val, $opt=array())
 * @method static integer countByTstamp($val, $opt=array())
 * @method static integer countByTitle($val, $opt=array())
 * @method static integer countByAlias($val, $opt=array())
 * @method static integer countByAuthor($val, $opt=array())
 * @method static integer countByDescription($val, $opt=array())
 * @method static integer countByAddImage($val, $opt=array())
 * @method static integer countBySingleSRC($val, $opt=array())
 * @method static integer countByAlt($val, $opt=array())
 * @method static integer countBySize($val, $opt=array())
 * @method static integer countByImagemargin($val, $opt=array())
 * @method static integer countByImageUrl($val, $opt=array())
 * @method static integer countByFullsize($val, $opt=array())
 * @method static integer countByCaption($val, $opt=array())
 * @method static integer countByFloating($val, $opt=array())
 * @method static integer countByAddEnclosure($val, $opt=array())
 * @method static integer countByEnclosure($val, $opt=array())
 * @method static integer countByNoComments($val, $opt=array())
 * @method static integer countByPublished($val, $opt=array())
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ProductModel extends \Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_product';

	/**
	 * Find a published Product from one or more categories by its ID or alias
	 *
	 * @param mixed $varId      The numeric ID or alias name
	 * @param array $arrPids    An array of parent IDs
	 * @param array $arrOptions An optional options array
	 *
	 * @return ProductModel|null The model or null if there is no Product
	 */
	public static function findPublishedByParentAndIdOrAlias($varId, $arrPids, array $arrOptions=array())
	{
		if (empty($arrPids) || !\is_array($arrPids))
		{
			return null;
		}

		$t = static::$strTable;
		$arrColumns = !preg_match('/^[1-9]\d*$/', $varId) ? array("$t.alias=?") : array("$t.id=?");
		$arrColumns[] = "$t.pid IN(" . implode(',', array_map('\intval', $arrPids)) . ")";

		if (!static::isPreviewMode($arrOptions))
		{
			$arrColumns[] = "$t.published='1'";
		}

		return static::findOneBy($arrColumns, $varId, $arrOptions);
	}

	/**
	 * Find all published Products by their parent ID
	 *
	 * @param int   $intPid     The parent ID
	 * @param array $arrOptions An optional options array
	 *
	 * @return Collection|ProductModel[]|ProductModel|null A collection of models or null if there are no Products
	 */
	public static function findPublishedByPid($intPid, array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.pid=?");

		if (!static::isPreviewMode($arrOptions))
		{
			$arrColumns[] = "$t.published='1'";
		}

		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}

		return static::findBy($arrColumns, $intPid, $arrOptions);
	}

	/**
	 * Find all published Products by their parent IDs
	 *
	 * @param array $arrPids    An array of Product category IDs
	 * @param array $arrOptions An optional options array
	 *
	 * @return Collection|ProductModel[]|ProductModel|null A collection of models or null if there are no Products
	 */
	public static function findPublishedByPids($arrPids, array $arrOptions=array())
	{
		if (empty($arrPids) || !\is_array($arrPids))
		{
			return null;
		}

		$t = static::$strTable;
		$arrColumns = array("$t.pid IN(" . implode(',', array_map('\intval', $arrPids)) . ")");

		if (!static::isPreviewMode($arrOptions))
		{
			$arrColumns[] = "$t.published='1'";
		}

		if (isset($arrOptions['organic']))
		{
			$arrColumns[] = "$t.organic='1'";
		}

		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.pid, $t.sorting";
		}

		return static::findBy($arrColumns, null, $arrOptions);
	}
}

class_alias(ProductModel::class, 'ProductModel');
