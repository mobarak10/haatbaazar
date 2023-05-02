<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('role.index')" :create-route="route('role.create')" />
            <div class="container mt-2">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span>{{ lang.role_details }}</span>
                        <div>
                            <inertia-link :href="route('role.edit', role.id)" class="btn table-small-button ms-2" :title="lang.edit">
                                <i class="bi bi-pencil"></i>
                            </inertia-link>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="row">
                                        <div class="col-12 text-center border-bottom">
                                            <h3>{{ lang.name }}: {{ role.name }}</h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div v-for="(menu, index) in menus" :key="index" class="col-4 mt-2">
                                        <div class="card h-100">
                                            <div class="card-header d-flex justify-content-between">
                                                <span>{{ menu.name }}</span>
                                            </div>

                                            <div class="card-body">
                                                <div v-for="(permission, permissionIndex) in permissions" :key="permissionIndex">
                                                    <div v-if="menu.slug === permission.name.split('-')[0]" class="form-check">
                                                        <input type="checkbox" v-model="form.permissions"
                                                               class="form-check-input"
                                                               :value="permission.id"
                                                               @click.prevent=""
                                                               :id="'permission-' + permission.id">

                                                        <label class="form-check-label"
                                                               aria-describedby="permission-help"
                                                               :for="'permission-' + permission.id">
                                                            {{ permission.name.split('-')[1].substring(0,1).toUpperCase() + permission.name.split('-')[1].substring(1) }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
export default {
    name: "Show",
    components: {
        BreezeAuthenticatedLayout,
        TopBar,
    },
    props: {
        role: Object,
        menus: Object,
        permissions: Object,
        selectedPermission: Object,
    },

    data() {
        return{
            lang:this.$page.props.lang,
            form: this.$inertia.form({
                id: null,
                name: null,
                permissions: [],
            })
        }
    },

    methods: {
        initialValues() {
            this.form.id = this.role.id
            this.form.name = this.role.name
            this.form.permissions = this.selectedPermission
        }
    },

    mounted() {
        console.log(this.selectedPermission)
        this.initialValues()
    }
}
</script>

<style scoped>

</style>
