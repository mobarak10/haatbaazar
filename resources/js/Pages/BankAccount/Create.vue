<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container print-none">
                <ul class="nav nav-tabs mt-2">
                    <li class="nav-item">
                        <inertia-link class="nav-link" :href="route('bank-account.index')">{{ lang.all_records }}</inertia-link>
                    </li>

                    <li class="nav-item">
                        <inertia-link class="nav-link active" :href="route('bank-account.create')">{{ lang.new_entry }}</inertia-link>
                    </li>
                </ul>
            </div>
            <div class="sale container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card mb-5 border-0">
                        <div class="card-header p-0 border-0 d-flex">
                            <!-- page title -->
                            <div class="mt-3">
                                <h4 class="main-title">{{ lang.create_new_bank_account }}</h4>
                                <p><small>{{ lang.all }} <span style="color: red">*</span> {{ lang.mark_must_be_required }}</small></p>
                            </div>

                            <!-- header icon -->
                            <inertia-link :href="route('bank-account.index')" :title="lang.go_back"
                                          class="pe-0 ms-auto print-none top-icon-button mb-2 mt-3">
                                <i class="bi bi-arrow-left"></i>
                            </inertia-link>
                        </div>
                    </div>

                    <form @submit.prevent="saveBankAccount()" method="POST">
                        <!-- type text -->
                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="bank_id" class="form-label required mt-1">{{ lang.bank_name }}</label>
                            </div>

                            <div class="col-6">
                                <select v-model="form.bank_id" id="bank_id" class="form-select">
                                    <option :value="null">{{ lang.choose_one }}</option>
                                    <option v-for="(bank, index) in banks" :value="bank.id" :key="index">{{ bank.name }}</option>
                                </select>
                                <div v-if="form.errors.bank_id" class="text-danger">{{ form.errors.bank_id }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="account_name" class="form-label required mt-1">{{ lang.account_owner_name }}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.account_name" id="account_name" class="form-control">
                                <div v-if="form.errors.account_name" class="text-danger">{{ form.errors.account_name }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="account_number" class="form-label required mt-1">{{ lang.account_number }}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.account_number" id="account_number" class="form-control">
                                <div v-if="form.errors.account_number" class="text-danger">{{ form.errors.account_number }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="branch" class="form-label required mt-1">{{ lang.branch }}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.branch" id="branch" class="form-control">
                                <div v-if="form.errors.branch" class="text-danger">{{ form.errors.branch }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="balance" class="form-label required mt-1">{{ lang.balance }}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.balance" id="balance" class="form-control">
                                <div v-if="form.errors.balance" class="text-danger">{{ form.errors.balance }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="note" class="form-label mt-1">{{ lang.description }}</label>
                            </div>

                            <div class="col-6">
                                <textarea v-model="form.note" class="form-control" id="note"></textarea>
                                <!--                                    <input type="text" v-model="form.note" class="form-control" id="note" placeholder="50000.00">-->
                                <div v-if="form.errors.note" class="text-danger">{{ form.errors.note }}</div>
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
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Button from "@/Components/Button";

export default {
    components: {
        Button,
        BreezeAuthenticatedLayout,
    },
    props: {
        banks: Object
    },
    data(){
        return {
            lang:this.$page.props.lang,
            form: this.$inertia.form({
                account_name: '',
                account_number: '',
                branch: '',
                balance: '',
                bank_id: null,
                note: '',
            }),
        }
    },
    methods:{
        saveBankAccount(){
            this.form.post(this.route('bank-account.store'))
        }
    },

    mounted() {
        console.log(this.banks)
    }

}
</script>
