<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace RackRuether;

use Contao\Model\Collection;

/**
 * Reads and writes Product categories
 *
 * @property integer $id
 * @property integer $tstamp
 * @property string  $title
 * @property string  $headline
 * @property integer $jumpTo
 * @property boolean $allowComments
 * @property string  $notify
 * @property string  $sortOrder
 * @property integer $perPage
 * @property boolean $moderate
 * @property boolean $bbcode
 * @property boolean $requireLogin
 * @property boolean $disableCaptcha
 *
 * @method static ProductCategoryModel|null findById($id, array $opt=array())
 * @method static ProductCategoryModel|null findByPk($id, array $opt=array())
 * @method static ProductCategoryModel|null findByIdOrAlias($val, array $opt=array())
 * @method static ProductCategoryModel|null findOneBy($col, $val, array $opt=array())
 * @method static ProductCategoryModel|null findOneByTstamp($val, array $opt=array())
 * @method static ProductCategoryModel|null findOneByTitle($val, array $opt=array())
 * @method static ProductCategoryModel|null findOneByHeadline($val, array $opt=array())
 * @method static ProductCategoryModel|null findOneByJumpTo($val, array $opt=array())
 * @method static ProductCategoryModel|null findOneByAllowComments($val, array $opt=array())
 * @method static ProductCategoryModel|null findOneByNotify($val, array $opt=array())
 * @method static ProductCategoryModel|null findOneBySortOrder($val, array $opt=array())
 * @method static ProductCategoryModel|null findOneByPerPage($val, array $opt=array())
 * @method static ProductCategoryModel|null findOneByModerate($val, array $opt=array())
 * @method static ProductCategoryModel|null findOneByBbcode($val, array $opt=array())
 * @method static ProductCategoryModel|null findOneByRequireLogin($val, array $opt=array())
 * @method static ProductCategoryModel|null findOneByDisableCaptcha($val, array $opt=array())
 *
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findByTstamp($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findByTitle($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findByHeadline($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findByJumpTo($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findByAllowComments($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findByNotify($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findBySortOrder($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findByPerPage($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findByModerate($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findByBbcode($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findByRequireLogin($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findByDisableCaptcha($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findMultipleByIds($val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findBy($col, $val, array $opt=array())
 * @method static Collection|ProductCategoryModel[]|ProductCategoryModel|null findAll(array $opt=array())
 *
 * @method static integer countById($id, array $opt=array())
 * @method static integer countByTstamp($val, array $opt=array())
 * @method static integer countByTitle($val, array $opt=array())
 * @method static integer countByHeadline($val, array $opt=array())
 * @method static integer countByJumpTo($val, array $opt=array())
 * @method static integer countByAllowComments($val, array $opt=array())
 * @method static integer countByNotify($val, array $opt=array())
 * @method static integer countBySortOrder($val, array $opt=array())
 * @method static integer countByPerPage($val, array $opt=array())
 * @method static integer countByModerate($val, array $opt=array())
 * @method static integer countByBbcode($val, array $opt=array())
 * @method static integer countByRequireLogin($val, array $opt=array())
 * @method static integer countByDisableCaptcha($val, array $opt=array())
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ProductCategoryModel extends Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_product_category';

}

class_alias(ProductCategoryModel::class, 'ProductCategoryModel');
