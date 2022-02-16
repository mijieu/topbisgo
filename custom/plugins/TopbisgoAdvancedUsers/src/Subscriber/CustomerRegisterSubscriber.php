<?php declare(strict_types=1);

namespace Topbisgo\AdvancedUsers\Subscriber;

use Shopware\Core\Checkout\Customer\Event\CustomerRegisterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Topbisgo\AdvancedUsers\Core\System\User\Service\UserRegistrationService;

class CustomerRegisterSubscriber implements EventSubscriberInterface
{
    private UserRegistrationService $userRegistrationService;

    public function __construct(UserRegistrationService $userRegistrationService)
    {
        $this->userRegistrationService = $userRegistrationService;
    }

    public static function getSubscribedEvents()
    {
        return [
            CustomerRegisterEvent::class => 'onCustomerRegister'
        ];
    }

    public function onCustomerRegister(CustomerRegisterEvent $event)
    {
        $customer = $event->getCustomer();
        $this->userRegistrationService->registerCustomerAsAdminUser($customer);

    }

}
