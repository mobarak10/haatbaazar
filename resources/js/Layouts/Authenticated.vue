<template>
    <div>
        <!-- sidebar -->
        <my-aside :showAside="showAside"/>
        <!-- End sidebar -->

        <!-- page-aside-layer -->
        <div class="page-aside-layer" @click.prevent="showAside  = ! showAside" :class="{show : !showAside}"></div>
        <div class="mainbar" :class="{expand : !showAside}">
            <my-header :showAside="showAside" @sliderClicked="showAside  = ! showAside" />
            <div>
                <flash-messages />
                <slot/>
            </div>
        </div>
    </div>
</template>

<script>
    import MyAside from '@/Components/Aside'
    import MyHeader from '@/Components/Header'
    import FlashMessages from '@/Components/FlashMessages'

    export default {
        components: {
            MyAside,
            MyHeader,
            FlashMessages,
        },

        data() {
            return {
                showingNavigationDropdown: false,
                showAside : true,
            }
        },

    }
</script>
