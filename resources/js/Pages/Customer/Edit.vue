<template>
    <div>
        <breeze-authenticated-layout>
            <!-- container menu -->
            <top-bar :index-route="route('customer.index')" :create-route="route('customer.create')" />
            <!-- container menu end -->

            <div class="container">
                <div class="card mb-5 border-0">
                    <div class="card-header p-0 border-0 d-flex">
                        <!-- page title -->
                        <div class="mt-3">
                            <h4 class="main-title">{{lang.create_new_party}}</h4>
                            <p><small>{{ lang.all}} <span style="color: red">*</span>{{lang.mark_must_be_required}}</small></p>
                        </div>

                        <!-- header icon -->
                        <back-icon :Url="route('customer.index')" />
                    </div>
                </div>

                <div class="card-body p-0 pt-3">
                    <form @submit.prevent="updateCustomer()" method="POST">
                        <!-- type text -->
                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="contact_person_name" class="form-label required mt-1">{{ lang.party_name}}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.name" class="form-control" id="contact_person_name" placeholder="Characters only">
                                <div v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="showroom" class="form-label mt-1">{{ lang.showroom }}</label>
                            </div>

                            <div class="col-6">
                                <select name="showroom" class="form-select" v-model="form.showroom_id" id="showroom">
                                    <option :value="null">{{ lang.select_showroom }}</option>
                                    <option v-for="(showroom, index) in showrooms" :key="index" :value="showroom.id">
                                        {{ showroom.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.showroom_id" class="text-danger">{{ form.errors.showroom_id }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="mokam_id" class="form-label mt-1">{{lang.mokam}}</label>
                            </div>

                            <div class="col-6">
                                <select name="mokam_id" class="form-select" v-model="form.mokam_id" id="mokam_id">
                                    <option :value="null">{{ lang.select_mokam }}</option>
                                    <option v-for="(mokam, index) in mokams" :key="index" :value="mokam.id">
                                        {{ mokam.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.mokam_id" class="text-danger">{{ form.errors.mokam_id }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="phone" class="form-label required mt-1">{{ lang.phone}}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.phone" class="form-control" id="phone" placeholder="Enter phone number">
                                <div v-if="form.errors.phone" class="text-danger">{{ form.errors.phone }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="email" class="form-label mt-1">{{ lang.email}}</label>
                            </div>

                            <div class="col-6">
                                <input type="email" v-model="form.email" class="form-control" id="email" :placeholder="lang.enter_email">
                                <div v-if="form.errors.email" class="text-danger">{{ form.errors.email }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="balance" class="form-label required mt-1">{{ lang.balance}}</label>
                            </div>

                            <div class="col-6">
                                <div class="input-group">
                                    <input type="number" step="any" min="0" v-model="form.balance" class="form-control" id="balance" placeholder="Enter balance">
                                    <div class="input-group-append">
                                        <select v-model="form.balance_status" class="form-select px-5">
                                            <option value="receivable">Receivable</option>
                                            <option value="payable">Payable</option>
                                        </select>
                                    </div>
                                </div>
                                <div v-if="form.errors.balance" class="text-danger">{{ form.errors.balance }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="address" class="form-label mt-1">{{ lang.address}}</label>
                            </div>

                            <div class="col-8">
                                <textarea v-model="form.address" class="form-control" id="address" rows="5" placeholder="Optional"></textarea>
                                <div v-if="form.errors.address" class="text-danger">{{ form.errors.address }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="description" class="form-label mt-1">{{ lang.description }}</label>
                            </div>

                            <div class="col-8">
                                <textarea v-model="form.description" class="form-control" id="description" rows="5" :placeholder="lang.optional"></textarea>
                                <div v-if="form.errors.description" class="text-danger">{{ form.errors.description }}</div>
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
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import BackIcon from "@/Pages/ReusableComponent/Icon/BackIcon";
export default {
    name: "Edit",
    components: {
        BreezeAuthenticatedLayout,
        TopBar,
        BackIcon,
    },
    props: {
        customer: Object,
        mokams: Array,
        showrooms: Array,
    },
    data() {
        return {
            lang: this.$page.props.lang,
            form: this.$inertia.form({
                id: null,
                genus: 'customer',
                name: null,
                description: null,
                balance_status: '',
                phone: null,
                email: null,
                address: null,
                mokam_id: null,
                showroom_id: null,
                balance: null,
            }),
        }
    },

    methods: {
        updateCustomer(){
            this.form.patch(this.route("customer.update", this.form.id), {
                preserveScroll: true,
            });
        },

        initialValues() {
            this.form.id = this.customer.id
            this.form.name = this.customer.name
            this.form.mokam_id = this.customer.mokam_id
            this.form.showroom_id = this.customer.showroom_id
            this.form.description = this.customer.description
            this.form.phone = this.customer.phone
            this.form.email = this.customer.email
            this.form.address = this.customer.address
            this.form.mokam = this.customer.mokam
            this.form.balance = Math.abs(this.customer.balance)
            if (this.customer.balance >= 0) {
                this.form.balance_status = 'receivable'
            }else{
                this.form.balance_status = 'payable'
            }
        },
    },

    mounted() {
        this.initialValues()
    }
}
</script>

<style scoped>

</style>
