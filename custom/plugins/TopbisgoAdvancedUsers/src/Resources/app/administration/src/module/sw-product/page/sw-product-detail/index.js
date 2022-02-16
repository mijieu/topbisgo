import template from './sw-product-detail-override.html.twig';

const { Criteria } = Shopware.Data;
const config = Object.create(Shopware.Component.getComponentRegistry().get('sw-product-detail'));

Shopware.Component.override('sw-product-detail', {
    template,
    computed: {
        canEditAdvancedPricing() {
            return !this.acl.can('product_advanced_pricing:deny') || this.acl.isAdmin();
        },
        productCriteria() {
            const criteria = config.computed.productCriteria.bind(this)();

            if (this.acl.can('product_only_own:all') && !this.acl.isAdmin()) {
                criteria.addFilter(Criteria.equals(
                    'product.productExtension.createdById',
                    Shopware.State.get('session').currentUser.id
                ));
            }

            return criteria;
        },
    },
    data() {
        const data = config.data.bind(this)();

        data.userId = Shopware.State.get('session').currentUser.id;

        return data;
    },
    methods: {
        onSave() {
            if (!this.can('all')) {
                this.onCancel();
            }

            if (this.acl.can('product_stock:deny')) {
                this.product.stock = 1;
            }

            if (!this.productId) {
                const productExtensionRepository = this.repositoryFactory.create(
                    'topbisgo_custom_role_product_extension',
                );

                const productExtension = productExtensionRepository.create(Shopware.Context.api);
                productExtension.createdById = this.userId;
                this.product.extensions.productExtension = productExtension;
            }

            config.methods.onSave.bind(this)();
        },
        can(privilege) {
            return this.acl.isAdmin() ||
                (
                    this.acl.can(`product_only_own:${privilege}`) &&
                    this.product.extensions?.productExtension?.createdById === this.userId
                );
        },
    },
});
