<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('bank-account.index')" :create-route="route('bank-account.create')" />
            <div class="sale container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card mb-5 border-0">
                        <div class="card-header p-0 border-0 d-flex">
                            <!-- page title -->
                            <div class="mt-3">
                                <h4 class="main-title">{{ lang.update_bank_account }}</h4>
                                <p><small>{{lang.all}}<span style="color: red">*</span>{{lang.mark_must_be_required}}</small></p>
                            </div>

                            <!-- header icon -->
                            <inertia-link :href="route('bank-account.index')" :title="lang.go_back"
                                          class="pe-0 ms-auto print-none top-icon-button mb-2 mt-3">
                                <i class="bi bi-arrow-left"></i>
                            </inertia-link>
                        </div>
                    </div>

                    <form @submit.prevent="updateBankAccount()" method="POST">
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
import Button from "@/Components/Button";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";

export default {
    name: "Edit",
    components: {
        Button,
        BreezeAuthenticatedLayout,
        TopBar,
    },
    props: {
        banks: Object,
        bankAccount: Object
    },
    data(){
        return {
            lang:this.$page.props.lang,
            form: this.$inertia.form({
                id: null,
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
        updateBankAccount(){
            this.form.patch(this.route("bank-account.update", this.form.id), {
                preserveScroll: true,
            });
        },

        initialValues() {
            this.form.id = this.bankAccount.id
            this.form.account_name = this.bankAccount.account_name
            this.form.account_number = this.bankAccount.account_number
            this.form.branch = this.bankAccount.branch
            this.form.balance = this.bankAccount.balance
            this.form.bank_id = this.bankAccount.bank_id
            this.form.note = this.bankAccount.note
        }
    },

    mounted() {
        this.initialValues()
        console.log(this.bankAccount)
    }
}
</script>

<style scoped>

</style>
