<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('user.index')" :create-route="route('user.create')" />
            <div class="sale container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card mb-5 border-0">
                        <div class="card-header p-0 border-0 d-flex">
                            <!-- page title -->
                            <div class="mt-3">
                                <h4 class="main-title">{{ lang.create_new_user }}</h4>
                                <p><small>{{ lang.all }} <span style="color: red">*</span> {{ lang.mark_must_be_required }}</small></p>
                            </div>

                            <!-- header icon -->
                            <back-icon :Url="route('user.index')" />
                        </div>
                    </div>

                    <form @submit.prevent="saveUser()" method="POST">
                        <!-- type text -->
                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="name" class="form-label required mt-1">{{ lang.name }}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.name" class="form-control" id="name" :placeholder="lang.enter_godown_name">
                                <div v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="phone" class="form-label mt-1">{{ lang.phone }}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.phone" class="form-control" id="phone" :placeholder="lang.phone">
                                <div v-if="form.errors.phone" class="text-danger">{{ form.errors.phone }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="email" class="form-label mt-1">{{ lang.email }}</label>
                            </div>

                            <div class="col-6">
                                <input type="email" v-model="form.email" class="form-control" id="email" :placeholder="lang.enter_email">
                                <div v-if="form.errors.email" class="text-danger">{{ form.errors.email }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="salary" class="form-label mt-1">{{ lang.salary }}</label>
                            </div>

                            <div class="col-6">
                                <input type="number" v-model="form.salary" class="form-control" id="salary" :placeholder="lang.salary">
                                <div v-if="form.errors.salary" class="text-danger">{{ form.errors.salary }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="password" class="form-label mt-1">{{ lang.password }}</label>
                            </div>

                            <div class="col-6">
                                <input type="password" v-model="form.password" class="form-control" id="password" :placeholder="lang.enter_password">
                                <div v-if="form.errors.password" class="text-danger">{{ form.errors.password }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="password_confirmation" class="form-label mt-1">{{ lang.confirm_password }}</label>
                            </div>

                            <div class="col-6">
                                <input type="password" v-model="form.password_confirmation" class="form-control" id="password_confirmation" :placeholder="lang.enter_password_confirmation">
                                <div v-if="form.errors.password_confirmation" class="text-danger">{{ form.errors.password_confirmation }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label class="form-label mt-1">{{ lang.role }}</label>
                            </div>

                            <div class="col-6">
                                <div class="row">
                                    <div v-for="(role, index) in roles" class="col-md-3" :key="index">
                                        <div class="form-check">
                                            <input type="checkbox"
                                                   v-model="form.roles"
                                                   class="form-check-input"
                                                   :value="role.id"
                                                   :id="'role-'+role.id">

                                            <label class="form-check-label"
                                                   aria-describedby="role-help"
                                                   :for="'role-'+role.id">
                                                {{ role.name }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="form.errors.roles" class="text-danger">{{ form.errors.roles }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label class="form-label mt-1 required">{{ lang.showroom }}</label>
                            </div>

                            <div class="col-6">
                                <div class="row">
                                    <div v-for="(showroom, index) in showrooms" class="col-md-3" :key="index">
                                        <div class="form-check">
                                            <input type="checkbox"
                                                   v-model="form.showrooms"
                                                   class="form-check-input"
                                                   :value="showroom.id"
                                                   :id="'showroom-'+showroom.id">

                                            <label class="form-check-label"
                                                   aria-describedby="role-help"
                                                   :for="'showroom-'+showroom.id">
                                                {{ showroom.name }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="form.errors.showrooms" class="text-danger">{{ form.errors.showrooms }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label class="form-label mt-1">&nbsp;</label>
                            </div>

                            <div class="col-10">
                                <button type="reset" class="btn btn-warning me-2">
                                    <i class="bi bi-dash-square"></i>
                                    <span class="p-1">{{ lang.reset }}</span>
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-square"></i>
                                    <span class="p-1">{{ lang.save }}</span>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import Button from "@/Components/Button";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import BackIcon from "@/Pages/ReusableComponent/Icon/BackIcon";

export default {
    name: "Edit",
    components: {
        Button,
        BreezeAuthenticatedLayout,
        TopBar,
        BackIcon,
    },
    props: {
        roles: Object,
        user: Object,
        user_roles: Object,
        showrooms:Object,
        user_showrooms:Object,
    },
    data(){
        return {
            lang:this.$page.props.lang,
            form: this.$inertia.form({
                id: null,
                name: null,
                email: null,
                phone: null,
                salary: null,
                roles: [],
                showrooms: [],
                password: null,
                password_confirmation: null,
            }),
        }
    },
    methods:{
        saveUser(){
            this.form.patch(this.route('user.update', this.form.id))
        },

        initialValues() {
            this.form.id = this.user.id
            this.form.name = this.user.name
            this.form.email = this.user.email
            this.form.phone = this.user.phone
            this.form.salary = this.user.salary
            this.form.roles = this.user_roles
            this.form.showrooms = this.user_showrooms
        },
    },

    mounted() {
        this.initialValues()
    }
}
</script>

<style scoped>

</style>
