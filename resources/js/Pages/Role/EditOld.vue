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
                                                               :id="'permission-' + permission.id">

                                                        <label class="form-check-label"
                                                               aria-describedby="permission-help"
                                                               :for="'permission-' + permission.id">
                                                            {{ permission.name.split('-')[1].substring(0,1).toUpperCase() + permission.name.split('-')[1].substring(1).replace(/_/g, " ") }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

export default {
    name: "Edit",
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
        updateRole() {
            this.form.patch(this.route('role.update', this.form.id))
        },

        initialValues() {
            this.form.id = this.role.id
            this.form.name = this.role.name
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
