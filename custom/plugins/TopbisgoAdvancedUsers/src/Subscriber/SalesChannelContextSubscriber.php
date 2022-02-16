<?php declare(strict_types=1);

namespace Topbisgo\AdvancedUsers\Subscriber;

use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SalesChannelContextSubscriber implements EventSubscriberInterface
{
    public const PLUGIN_CONFIG = 'TopbisgoAdvancedUsers';

    /** @var SystemConfigService */
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents()
    {
        return [
            StorefrontRenderEvent::class => 'onStorefrontRender'
        ];
    }

    public function onStorefrontRender(StorefrontRenderEvent $event)
    {
        $pluginConfig = $this->systemConfigService->get(self::PLUGIN_CONFIG);
        if (!$pluginConfig) {
            return;
        }
        $event->getSalesChannelContext()->addExtension('topbsigoConfig', new ArrayStruct($pluginConfig['config']));
    }
}
