<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace RackRuether\ProductBundle\Picker;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\CoreBundle\Picker\AbstractPickerProvider;
use Contao\CoreBundle\Picker\DcaPickerProviderInterface;
use Contao\CoreBundle\Picker\PickerConfig;
use RackRuether\ProductCategoryModel;
use RackRuether\ProductModel;

class ProductPickerProvider extends AbstractPickerProvider implements DcaPickerProviderInterface, FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'productPicker';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsContext($context): bool
    {
        return 'link' === $context && $this->getUser()->hasAccess('product', 'modules');
    }

    /**
     * {@inheritdoc}
     */
    public function supportsValue(PickerConfig $config): bool
    {
        return false !== strpos($config->getValue(), '{{product_url::');
    }

    /**
     * {@inheritdoc}
     */
    public function getDcaTable(): string
    {
        return 'tl_product';
    }

    /**
     * {@inheritdoc}
     */
    public function getDcaAttributes(PickerConfig $config): array
    {
        $attributes = ['fieldType' => 'radio'];

        if ($source = $config->getExtra('source')) {
            $attributes['preserveRecord'] = $source;
        }

        if ($this->supportsValue($config)) {
            $attributes['value'] = str_replace(['{{product_url::', '}}'], '', $config->getValue());
        }

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function convertDcaValue(PickerConfig $config, $value): string
    {
        return '{{product_url::'.$value.'}}';
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteParameters(PickerConfig $config = null): array
    {
        $params = ['do' => 'product'];

        if (null === $config || !$config->getValue() || false === strpos($config->getValue(), '{{product_url::')) {
            return $params;
        }

        $value = str_replace(['{{product_url::', '}}'], '', $config->getValue());

        if (null !== ($productId = $this->getProductCategoryId($value))) {
            $params['table'] = 'tl_product';
            $params['id'] = $productId;
        }

        return $params;
    }

    /**
     * @param int|string $id
     */
    private function getProductCategoryId($id): ?int
    {
        /** @var ProductModel $productAdapter */
        $productAdapter = $this->framework->getAdapter(ProductModel::class);

        if (!($productModel = $productAdapter->findById($id)) instanceof ProductModel) {
            return null;
        }

        if (!($productCategory = $productModel->getRelated('pid')) instanceof ProductCategoryModel) {
            return null;
        }

        return (int) $productCategory->id;
    }
}
