<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace RackRuether\ProductBundle\EventListener;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use RackRuether\ProductBundle\ProductCategoryModel;
use RackRuether\ProductBundle\ProductModel;
use Contao\PageModel;
use Contao\StringUtil;

class InsertTagsListener
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Replaces the Product insert tags.
     *
     * @return string|false
     */
    public function onReplaceInsertTags(string $tag, bool $useCache, $cacheValue, array $flags)
    {
        static $supportedTags = [
            'product',
            'product_open',
            'product_url',
            'product_title',
        ];

        $elements = explode('::', $tag);
        $key = strtolower($elements[0]);

        if (!\in_array($key, $supportedTags, true)) {
            return false;
        }

        $this->framework->initialize();

        /** @var ProductModel $adapter */
        $adapter = $this->framework->getAdapter(ProductModel::class);

        $product = $adapter->findByIdOrAlias($elements[1]);

        if (null === $product || false === ($url = $this->generateUrl($product, \in_array('absolute', $flags, true)))) {
            return '';
        }

        return $this->generateReplacement($product, $key, $url);
    }

    /**
     * @return string|false
     */
    private function generateUrl(ProductModel $product, bool $absolute)
    {
        /** @var PageModel $jumpTo */
        if (
            !($category = $product->getRelated('pid')) instanceof ProductCategoryModel
            || !(($jumpTo = $category->getRelated('jumpTo')) instanceof PageModel)
        ) {
            return false;
        }

        /** @var Config $config */
        $config = $this->framework->getAdapter(Config::class);
        $params = ($config->get('useAutoItem') ? '/' : '/items/').($product->alias ?: $product->id);

        return $absolute ? $jumpTo->getAbsoluteUrl($params) : $jumpTo->getFrontendUrl($params);
    }

    /**
     * @return string|false
     */
    private function generateReplacement(ProductModel $product, string $key, string $url)
    {
        switch ($key) {
            case 'product':
                return sprintf(
                    '<a href="%s" title="%s">%s</a>',
                    $url,
                    StringUtil::specialchars($product->title),
                    $product->title
                );

            case 'product_open':
                return sprintf(
                    '<a href="%s" title="%s">',
                    $url,
                    StringUtil::specialchars($product->title)
                );

            case 'product_url':
                return $url;

            case 'product_title':
                return StringUtil::specialchars($product->title);
        }

        return false;
    }
}
