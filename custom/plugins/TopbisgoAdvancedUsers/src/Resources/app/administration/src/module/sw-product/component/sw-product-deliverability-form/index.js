import template from './sw-product-deliverability-form-override.html.twig';

Shopware.Component.override('sw-product-deliverability-form', {
    template,
    inject: ['acl'],
})
