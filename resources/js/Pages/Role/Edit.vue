<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('role.index')" :create-route="route('role.create')" />

            <div class="container mt-2">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <span>{{ lang.create_a_new_role }}</span>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="updateRole()" method="POST">
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="row">
                                        <div class="col-12 mt-2">
                                            <label for="name" class="form-label required">{{ lang.role_name }}</label>
                                            <input type="text" v-model="form.name" class="form-control" id="name"
                                                   placeholder="Enter role name" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <h3 class="text-center mt-2">
                                        {{lang. permissions}}
                                        <a :href="route('permission.generate.all')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                                                <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                                                <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
                                            </svg>
                                        </a>
                                    </h3>
                                </div>

                                <div class="row mt-2">
                                    <div class="row mt-2">
                                        <permission-component ref="permission" v-for="(menu, index) in menus" :menu="menu" :key="index" :permissions="menuPermissions(menu)" :menus="menus"/>
                                    </div>
                                </div>

                            </div>
                            <div class="text-end">
                                <button type="reset" class="btn btn-block btn-danger m-2">{{ lang.reset }}</button>
                                <button type="submit" class="btn btn-block btn-primary">{{ lang.update }}</button>
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
import PermissionComponent from "@/Pages/ReusableComponent/PermissionComponent";
import {mapMutations, mapState} from "vuex";

export default {
    name: "Edit",
    components: {
        BreezeAuthenticatedLayout,
        TopBar,
        PermissionComponent,
    },
    computed: {
        ...mapState(['rolePermissions'])
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
        ...mapMutations(['mutationAddPermissions']),
        updateRole() {
            this.form.permissions = this.rolePermissions
            this.form.patch(this.route('role.update', this.form.id))
        },

        menuPermissions(menu){
            return this.permissions.filter(permission => permission.name.split('-')[0] == menu.slug)
        },

        initialValues() {
            this.form.id = this.role.id
            this.form.name = this.role.name
            this.selectedPermission.forEach(permission => {
                this.mutationAddPermissions(permission)
            })
            this.form.permissions = this.selectedPermission
        }
    },

    mounted() {
        this.initialValues()
    }
}
</script>

<style scoped>

</style>
