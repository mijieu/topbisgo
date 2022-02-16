<?php declare(strict_types=1);

namespace Topbisgo\AdvancedUsers;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Topbisgo\AdvancedUsers\Service\PrivilegesSet;

class TopbisgoAdvancedUsers extends Plugin
{
    protected const ROLE_NAME = 'TopbisgoCustomRole';

    public function activate(ActivateContext $context): void
    {
        $aclRoleRepository = $this->container->get('acl_role.repository');

        $aclRoleRepository->create([
            [
                'name' => self::ROLE_NAME,
                'description' => 'The role allows user read, write and delete products',
                'privileges' => PrivilegesSet::PRODUCT_ALL,
            ],
        ], $context->getContext());
    }

    public function deactivate(DeactivateContext $context): void
    {
        $aclRoleRepository = $this->container->get('acl_role.repository');

        $roleId = $aclRoleRepository->searchIds(
            (new Criteria())->addFilter(new EqualsFilter('name', self::ROLE_NAME)),
            $context->getContext()
        )->firstId();

        if (is_null($roleId)) {
            return;
        }

        $aclRoleRepository->delete([['id' => $roleId]], $context->getContext());
    }

    public function uninstall(UninstallContext $context): void
    {
        if ($context->keepUserData()) {
            return;
        }

        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('DROP TABLE IF EXISTS `topbisgo_custom_role_product_extension`');
    }
}
