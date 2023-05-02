export default function priceQuantity(product, unit) {
    const unitRelation = unit.relation.split('/')
    let quantity = 1
    for (let i = 0; i < unitRelation.length; i++) {
        if (i >= product.price_type) {
            quantity *= parseFloat(unitRelation[i + 1]) || '1'
        }
    }
    return product.quantity;
}
