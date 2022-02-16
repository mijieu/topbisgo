<?php declare(strict_types=1);

namespace Topbisgo\AdvancedUsers\Core\Content\Product\Extension;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\User\UserDefinition;

class ProductExtensionDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'topbisgo_custom_role_product_extension';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductExtensionEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            new FkField('product_id', 'productId', ProductDefinition::class),
            new FkField('created_by_id', 'createdById', UserDefinition::class),

            new OneToOneAssociationField('product', 'product_id', 'id', ProductDefinition::class, false),
            new OneToOneAssociationField('createdBy', 'created_by_id', 'id', UserDefinition::class, false),
        ]);
    }
}
