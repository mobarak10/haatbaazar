<template>
    <div>
        <h6 style="font-size: small">{{ product.name }}</h6>
        <svg id="batchBarcode"/>
        <h6 class="d-block mr-3">{{ $page.props.lang.price }} {{ product.sale_price}}</h6>
    </div>
</template>

<script>
export default {
    name: "BatchBarcode",
    props: {
        product: Object
    },
}
</script>

<style scoped>

</style>
