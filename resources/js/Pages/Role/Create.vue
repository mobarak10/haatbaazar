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
                        <form @submit.prevent="addRole()" method="POST">
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="row">
                                        <div class="col-12 mt-2">
                                            <label for="name" class="form-label required">{{ lang.role_name }}</label>
                                            <input type="text" v-model="form.name" class="form-control" id="name"
                                                   :placeholder="lang.enter_role_name" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="row">
                                        <h3 class="mt-2 text-center align-items-center">
                                            <span class="align-middle mx-2">{{lang.permissions}}</span>
                                            <inertia-link
                                                :href="route('permission.generate.all')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                                                    <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                                                    <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
                                                </svg>
                                            </inertia-link>
                                        </h3>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <permission-component v-for="(menu, index) in menus" :menu="menu" :key="index" :permissions="menuPermissions(menu)" :menus="menus"/>
                                </div>

                            </div>
                            <div class="text-end">
                                <button type="reset" class="btn btn-block btn-danger m-2">{{ lang.reset }}</button>
                                <button type="submit" class="btn btn-block btn-primary">{{ lang.save }}</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import PermissionComponent from "@/Pages/ReusableComponent/PermissionComponent";
import {mapState} from "vuex";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
export default {
    name: "Create",
    components: {
        BreezeAuthenticatedLayout,
        PermissionComponent,
        TopBar,
    },
    props: {
        menus: Object,
        permissions: Object,
    },
    computed: {
        ...mapState(['rolePermissions'])
    },
    data() {
        return{
            isAllChecked: false,
            lang:this.$page.props.lang,
            form: this.$inertia.form({
                name: null,
                permissions: [],
            })
        }
    },

    methods: {
        addRole() {
            this.form.permissions = this.rolePermissions
            this.form.post(this.route('role.store'))
        },
        menuPermissions(menu){
            return this.permissions.filter(permission => permission.name.split('-')[0] == menu.slug)
        },
    },
}
</script>

<style scoped>

</style>
