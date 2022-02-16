<?php declare(strict_types=1);

namespace Topbisgo\AdvancedUsers\Core\System\User\Service;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\System\User\Service\UserProvisioner;
use Shopware\Core\System\User\UserEntity;
use Doctrine\DBAL\Connection;

class UserRegistrationService
{
    public const PLUGIN_CONFIG = 'TopbisgoAdvancedUsers';

    private UserProvisioner $userProvisioner;

    /**
     * @var EntityRepositoryInterface
     */
    private $userRepository;

    private SystemConfigService $systemConfigService;

    private EntityRepositoryInterface $aclUserRole;

    private Connection $connection;

    public function __construct(UserProvisioner $userProvisioner, EntityRepositoryInterface $userRepository, SystemConfigService $systemConfigService, EntityRepositoryInterface $aclUserRole, Connection $connection)
    {
        $this->userProvisioner = $userProvisioner;
        $this->userRepository = $userRepository;
        $this->systemConfigService = $systemConfigService;
        $this->aclUserRole = $aclUserRole;
        $this->connection = $connection;
    }

    public function registerCustomerAsAdminUser(CustomerEntity $entity): void
    {
        $userName = strtolower($entity->getFirstName()) . "_" . strtolower($entity->getLastName());
        $password = "demo";
        $additionalData = [
            "firstName" => $entity->getFirstName(),
            "lastName" => $entity->getLastName(),
            "email" => $entity->getEmail(),
            "admin" => true
        ];
        $this->userProvisioner->provision($userName, $password, $additionalData);

        $this->updateUserInfo($userName, $entity->getPassword(),$entity->getDefaultShippingAddress()->getPhoneNumber());
    }

    private function updateUserInfo($userName, $password, $phone): void
    {
        $pluginConfig = $this->systemConfigService->get(self::PLUGIN_CONFIG);
        $customRole = $pluginConfig['config']['customRole'];

        $criteria = new Criteria();

        $criteria->addFilter(new EqualsFilter('username', $userName));
        /** @var UserEntity $userEntity */
        $userEntity = $this->userRepository->search($criteria, Context::createDefaultContext())->first();


        $this->userRepository->update([
            [
                'id' => $userEntity->getId(),
                'password' => $password,
                'admin' => false,
                'customFields' => [
                    'phoneNumber' => $phone ?? ""
                ]
            ],
        ], Context::createDefaultContext());

        $this->connection->insert('acl_user_role', [
            'user_id' => Uuid::fromHexToBytes($userEntity->getId()),
            'acl_role_id' => Uuid::fromHexToBytes($customRole),
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }

    private function isUserAlreadyRegistered(): bool
    {

        return true;
    }

}
