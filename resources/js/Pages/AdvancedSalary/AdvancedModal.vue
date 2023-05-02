<template>
    <!-- New advances salary Modal -->
    <div
        class="modal fade"
        id="payment-method"
        ref="modal"
        tabindex="-1"
        aria-labelledby="advanced-salary"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <form action="#" @submit.prevent="submitAdvancedSalary()">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="advanced-salary">
                            {{ lang.new }} {{ lang.advanced }} {{ lang.salary }}
                        </h5>
                        <button
                            type="button"
                            :disabled="form.processing"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <!-- advanced type start-->
                            <div class="col-12">
                                <label class="form-label required">
                                    {{ lang.advanced }} {{ lang.type }}
                                </label>
                                <br>
                                <div class="form-check form-check-inline">
                                    <input
                                        class=" form-check-input"
                                        type="radio"
                                        name="loan_type"
                                        id="loan-give"
                                        v-model="advancedType"
                                        value="give"/>

                                    <label class=" form-check-label" for="loan-give">
                                        {{ lang.advanced }} {{ lang.give }}
                                    </label>
                                </div>
                                <div class=" form-check form-check-inline">
                                    <input
                                        class=" form-check-input"
                                        type="radio"
                                        name="loan_type"
                                        id="loan-take"
                                        v-model="advancedType"
                                        value="take"/>
                                    <label class=" form-check-label" for="loan-take">
                                        {{ lang.advanced }} {{ lang.take }}
                                    </label>
                                </div>
                            </div>
                            <!-- advanced type end-->

                            <div class="col-12">
                                <label
                                    for="loan-account-id"
                                    class="form-label required">
                                    {{ lang.user }}
                                </label>
                                <v-select
                                    id="loan-account-id"
                                    v-model="form.user_id"
                                    :options="users"
                                    label="name"
                                    :clearable="false"
                                    :placeholder="lang.select_user"
                                    :reduce="(user) =>user.id">
                                </v-select>
                            </div>

                            <div class="col-12">
                                <label
                                    for="showroom"
                                    class="form-label">
                                    {{ lang.showroom }}
                                </label>
                                <v-select
                                    id="showroom"
                                    v-model="form.showroom_id"
                                    :options="showrooms"
                                    label="name"
                                    :clearable="false"
                                    :placeholder="lang.select_showroom"
                                    :reduce="(showroom) =>showroom.id">
                                </v-select>
                            </div>

                            <div class="col-12">
                                <label
                                    for="date"
                                    class="form-label required">
                                    {{ lang.date }}
                                </label>
                                <input
                                    type="date"
                                    class="form-control"
                                    v-model="form.date"
                                    id="date"
                                />
                                <div
                                    v-if="form.errors.date"
                                    class="text-danger"
                                >
                                    {{ form.errors.date }}
                                </div>
                            </div>

                            <div class="col-12">
                                <label
                                    for="amount"
                                    class="form-label required"
                                >{{ lang.amount }}</label
                                >
                                <input
                                    type="number"
                                    step="any"
                                    class="form-control"
                                    v-model="tempAmount"
                                    id="amount"
                                    min="0"
                                    :placeholder="lang.enter_amount"
                                />
                            </div>

                            <div class="col-12">
                                <cash-bank-component
                                    ref="cashBank"
                                    :bank-accounts="bankAccounts"
                                    :cashes="cashes" />
                            </div>

                            <div class="col-12">
                                <label
                                    for="note"
                                    class="form-label">
                                    {{ lang.note }}
                                </label>
                                <textarea
                                    class="form-control"
                                    v-model="form.note"
                                    id="note"
                                    :placeholder="lang.enter_note">
                                </textarea>
                                <div v-if="form.errors.note" class="text-danger">
                                    {{ form.errors.note }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            :disabled="form.processing"
                            class="btn custom-btn btn-danger"
                            data-bs-dismiss="modal">
                            {{ lang.close }}
                        </button>
                        <save-button :processing="form.processing" />
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End New advanced salary Modal -->
</template>

<script>
import alert from "@/alert";
import SaveButton from "@/Pages/ReusableComponent/Button/SaveButton";
import CashBankComponent from "@/Pages/ReusableComponent/CashBankComponent";
import "vue-select/dist/vue-select.css";
import vSelect from "vue-select";

export default {
    name: "AdvancedModal",
    props: {
        users: Object,
        cashes: Object,
        bankAccounts: Object,
        showrooms: Object,
        advancedSalary: Object,
    },
    watch: {
        advancedSalary: {
            deep: true,
            handler: function (value) {
                this.previousAdvancedSalary = value
            }
        },

        previousAdvancedSalary: {
            handler: 'editPreviousAdvanced',
            deep: true,
        },
    },
    components: {
        SaveButton,
        CashBankComponent,
        vSelect,
    },
    data() {
        return {
            previousAdvancedSalary: null,
            lang: this.$page.props.lang,
            modal: {},
            tempAmount: null,
            advancedType: 'give',
            form: this.$inertia.form({
                user_id: null,
                date: new Date().toISOString().substr(0, 10),
                amount: null,
                cash_id: null,
                bank_account_id: null,
                showroom_id: null,
                note: null,
                processing: false,
            })
        }
    },
    methods: {
        showModal() {
            this.previousAdvancedSalary = this.advancedSalary
            this.modal.show();
        },

        hideModal() {
            this.modal.hide();
        },

        resetModal() {
            this.$refs.cashBank.cash_id = null;
            this.$refs.cashBank.bank_account_id = null;
            this.$refs.cashBank.payment_method = 'cash';
            this.$refs.cashBank.cash_balance = null;
            this.$refs.cashBank.bank_balance = null;
            this.tempAmount = null;
            this.advancedType = 'give';
            this.previousAdvancedSalary = null;
            this.form = this.$inertia.form({
                user_id: null,
                processing: false,
                date: new Date().toISOString().substr(0, 10),
                amount: null,
                cash_id: null,
                showroom_id: null,
                bank_account_id: null,
                note: null,
            });
        },

        editPreviousAdvanced() {
            if (this.previousAdvancedSalary) {
                if (this.advancedSalary.amount > 0) {
                    this.advancedType = 'take'
                }else{
                    this.advancedType = 'give'
                }
                if(this.advancedSalary.cash_id) {
                    this.$refs.cashBank.cash_id = this.advancedSalary.cash_id
                }else{
                    this.$refs.cashBank.payment_method = 'bank'
                    this.$refs.cashBank.bank_account_id = this.advancedSalary.bank_account_id
                }
                this.tempAmount = Math.abs(this.advancedSalary.amount)
                this.form = this.$inertia.form({
                    ...this.advancedSalary,
                });
            }
        },

        submitAdvancedSalary() {
            this.form.processing = true
            this.form.cash_id = this.$refs.cashBank.cash_id
            this.form.bank_account_id = this.$refs.cashBank.bank_account_id
            if (this.advancedType === "give") {
                this.form.amount = -1 * this.tempAmount;
            } else {
                this.form.amount = this.tempAmount;
            }
            if (this.advancedSalary) {
                this.updateAdvanced();
            }else{
                this.storeAdvanced();
            }
        },
        updateAdvanced() {
            this.form.put(this.route("advanced-salary.update", this.advancedSalary.id), {
                preserveScroll: true,
                onSuccess: () => {
                    let toast = alert()
                    toast.fire({
                        icon: 'success',
                        title: 'Advanced salary updated successfully!'
                    })
                    this.form.processing = false
                    this.hideModal()
                },
                onError: () => this.form.processing = false
            });
        },
        storeAdvanced() {
            this.form.post(this.route("advanced-salary.store"), {
                preserveScroll: true,
                onSuccess: () => {
                    let toast = alert()
                    toast.fire({
                        icon: 'success',
                        title: 'Advanced salary given successfully!'
                    })
                    this.form.processing = false
                    this.hideModal()
                },
                onError: () => this.form.processing = false
            });
        },
    },
    mounted() {
        this.modal = new bootstrap.Modal(this.$refs.modal);
        this.$refs.modal.addEventListener("hidden.bs.modal", () =>
            this.resetModal()
        );
    }
}
</script>

<style scoped>

</style>
