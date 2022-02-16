<?php declare(strict_types=1);

namespace Topbisgo\AdvancedUsers\Core\Content\Product\Extension;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class ProductExtensionCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ProductExtensionEntity::class;
    }
}
