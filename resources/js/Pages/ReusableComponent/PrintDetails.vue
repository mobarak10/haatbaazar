<template>
    <div class="col-12 d-none d-print-block">
        <div class="text-center mb-2">
            <strong>{{ $page.props.config.print_details['name'] }}</strong>
            <br>
            <span>{{ $page.props.config.print_details['address'] }}</span>
            <br>
            <span>{{ $page.props.config.print_details['telephone'] }}</span>
            <br>
            <span>{{ $page.props.config.print_details['mobile']  }}</span>
        </div>
    </div>
</template>

<script>
export default {
    name: "PrintDetails"
}
</script>

<style scoped>

</style>
