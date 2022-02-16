import template from './sw-product-price-form-override.html.twig';

Shopware.Component.override('sw-product-price-form', {
    template,
    inject: ['acl'],
})
