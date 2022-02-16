<?php declare(strict_types=1);

namespace Topbisgo\AdvancedUsers\Subscriber;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\User\UserEntity;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Topbisgo\AdvancedUsers\Core\Content\Product\Extension\ProductExtensionEntity;

class ProductPageSubscriber implements EventSubscriberInterface
{
    private EntityRepositoryInterface $topbisgoRepository;
    private EntityRepositoryInterface $userRepository;
    private EntityRepositoryInterface $customerRepository;

    public function __construct(EntityRepositoryInterface $topbisgoRepository, EntityRepositoryInterface $userRepository, EntityRepositoryInterface $customerRepository)
    {
        $this->topbisgoRepository = $topbisgoRepository;
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            ProductPageLoadedEvent::class => 'onPDPLoaded'
        ];
    }

    public function onPDPLoaded(ProductPageLoadedEvent $event)
    {
        $product = $event->getPage()->getProduct();
        if (!$product->getExtension('productExtension')) {
            return;
        }
        $userId = $product->getExtension('productExtension')->getCreatedById();
        if ($userId) {
            $userCriteria = new Criteria();
            $userCriteria->addFilter(new EqualsFilter('id', $userId));
            $userEntities = $this->userRepository->search($userCriteria, $event->getContext());
            if (count($userEntities)) {
                /** @var UserEntity $user */
                $user = $userEntities->first();

                $customerCriteria = new Criteria();
                $customerCriteria->addFilter(new EqualsFilter('email', $user->getEmail()));
                $customerCriteria->addAssociation('defaultBillingAddress');
                /** @var CustomerEntity $customer */
                $customer = $this->customerRepository->search($customerCriteria, $event->getContext())->first();
                $info = [
                    'authorName' => $user->getFirstName(),
                    'authorLastName' => $user->getLastName(),
                    'email' => $user->getEmail(),
                    'phone' => $customer->getDefaultBillingAddress()->getPhoneNumber()
                ];
                $product->addExtension('info', new ArrayStruct($info));
            }
        }

    }
}
