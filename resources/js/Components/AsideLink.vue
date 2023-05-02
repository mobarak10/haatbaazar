<template>
    <li class="accordion-item">
        <a
            href="#"
            class="accordion-button collapsed"
            data-bs-toggle="collapse"
            :data-bs-target="dataTarget"
            aria-expanded="false"
            :aria-controls="ariaControl"
        >
            <i :class="icon"></i>
            <span>{{ data }}</span>
        </a>

        <ul
            :id="id"
            class="accordion-collapse collapse"
            :class="{
                        show:
                            showUrl.includes($page.props.currentUrl)
                    }"
            aria-labelledby="headingThree"
            data-bs-parent="#asideAccordion"
        >
            <slot/>
        </ul>
    </li>
</template>

<script>
import Link from "@/Components/Link";
export default {
    name: "AsideLink",
    components: {
        Link,
    },
    props: {
        dataTarget: String,
        ariaControl: String,
        id: String,
        data: String,
        showUrl: Array,
        icon: String,
    },
    data() {
        return{
            lang: this.$page.props.lang
        }
    },
    mounted() {
    }
}
</script>

<style scoped>

</style>
