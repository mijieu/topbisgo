<?php declare(strict_types=1);

namespace Topbisgo\AdvancedUsers\Core\Content\Product;

use Topbisgo\AdvancedUsers\Core\Content\Product\Extension\ProductExtensionDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductExtension extends EntityExtension
{
    /**
     * @inheritDoc
     */
    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add((new OneToOneAssociationField(
            'productExtension',
            'id',
            'product_id',
            ProductExtensionDefinition::class,
            true,
        ))->addFlags(new CascadeDelete(), new ApiAware()));
    }
}
