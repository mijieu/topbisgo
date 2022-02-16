const {Criteria} = Shopware.Data;
const config = Object.create(Shopware.Component.getComponentRegistry().get('sw-product-list'));

Shopware.Component.override('sw-product-list', {
    computed: {
        productCriteria() {
            const criteria = config.computed.productCriteria.bind(this)();
            console.log(this.acl);
            if (this.acl.can('product_only_own:all') && !this.acl.isAdmin()) {
                criteria.addFilter(Criteria.equals(
                    'product.productExtension.createdById',
                    Shopware.State.get('session').currentUser.id
                ));
            }
            return criteria;
        }
    }
});
