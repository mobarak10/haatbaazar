<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('loan.index')" :modal="showModal" />

            <div class="container sale">
                <!-- table -->
                <div class="border-0 card">
                    <div
                        class="p-0 py-1 border-0  card-header d-sm-flex d-block align-items-center"
                    >
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_loan }}</h4>
                            <br />
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->

                        <button
                            type="button"
                            role="button"
                            class="btn top-icon-button print-none ms-auto"
                            :title="lang.create_new_loan"
                            @click="onClickNewEntry"
                        >
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>

                    <!-- New Category Modal -->
                    <div
                        class="modal fade"
                        id="payment-method"
                        ref="modal"
                        tabindex="-1"
                        aria-labelledby="paymentMethodLabel"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog">
                            <form action="#" @submit.prevent="submitForm()">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5
                                            class="modal-title"
                                            id="paymentMethodLabel"
                                        >
                                            {{ lang.add_new_loan }}
                                        </h5>
                                        <button
                                            type="button"
                                            :disabled="form.processing"
                                            class="btn-close"
                                            data-bs-dismiss="modal"
                                            aria-label="Close"
                                        ></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label
                                                    class="form-label required"
                                                    >{{ lang.loan_type }}</label
                                                >
                                                <div
                                                    :class="{
                                                        'is-invalid':
                                                            form.loan_installments_count >
                                                            0,
                                                    }"
                                                >
                                                    <div
                                                        class=" form-check form-check-inline"
                                                    >
                                                        <input
                                                            class=" form-check-input"
                                                            type="radio"
                                                            name="loan_type"
                                                            id="loan-give"
                                                            v-model="loanType"
                                                            value="give"
                                                            :disabled="
                                                                form.loan_installments_count >
                                                                0
                                                            "
                                                        />
                                                        <label
                                                            class=" form-check-label"
                                                            for="loan-give"
                                                            >{{ lang.loan }} {{ lang.give }}</label
                                                        >
                                                    </div>
                                                    <div
                                                        class=" form-check form-check-inline"
                                                    >
                                                        <input
                                                            class=" form-check-input"
                                                            type="radio"
                                                            name="loan_type"
                                                            id="loan-take"
                                                            v-model="loanType"
                                                            value="take"
                                                            :disabled="
                                                                form.loan_installments_count >
                                                                0
                                                            "
                                                        />
                                                        <label
                                                            class=" form-check-label"
                                                            for="loan-take"
                                                            >{{ lang.loan }} {{ lang.take }}</label
                                                        >
                                                    </div>
                                                </div>
                                                <div class="invalid-feedback">
                                                    This loan already took
                                                    installment. So you can't
                                                    change it.
                                                </div>
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
                                                    for="loan-account-id"
                                                    class="form-label required"
                                                    >{{ lang.loan_account }}</label
                                                >
                                                <v-select
                                                    id="loan-account-id"
                                                    v-model="
                                                        form.loan_account_id
                                                    "
                                                    :options="loanAccounts"
                                                    label="name"
                                                    :clearable="false"
                                                    :placeholder="lang.select_loan_account"
                                                    :reduce="
                                                        (loanAccuont) =>
                                                            loanAccuont.id
                                                    "
                                                    :filterBy="
                                                        (
                                                            option,
                                                            label,
                                                            search
                                                        ) => {
                                                            return (
                                                                (
                                                                    option.name ||
                                                                    ''
                                                                )
                                                                    .toLocaleLowerCase()
                                                                    .indexOf(
                                                                        search.toLocaleLowerCase()
                                                                    ) > -1 ||
                                                                (
                                                                    option.phone ||
                                                                    ''
                                                                )
                                                                    .toLocaleLowerCase()
                                                                    .indexOf(
                                                                        search.toLocaleLowerCase()
                                                                    ) > -1
                                                            );
                                                        }
                                                    "
                                                >
                                                    <template
                                                        v-slot:option="option"
                                                    >
                                                        {{ option.name }} -
                                                        {{ option.phone }}
                                                    </template>
                                                </v-select>
                                                <div
                                                    v-if="
                                                        form.errors
                                                            .loan_account_id
                                                    "
                                                    class="text-danger"
                                                >
                                                    {{
                                                        form.errors
                                                            .loan_account_id
                                                    }}
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <label
                                                    for="date"
                                                    class="form-label required"
                                                    >{{ lang.date }}</label
                                                >
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
                                                    for="expired-date"
                                                    class="form-label required"
                                                    >{{ lang.expired_date }}</label
                                                >
                                                <input
                                                    type="date"
                                                    class="form-control"
                                                    v-model="form.expired_date"
                                                    id="expired-date"
                                                />
                                                <div
                                                    v-if="
                                                        form.errors.expired_date
                                                    "
                                                    class="text-danger"
                                                >
                                                    {{
                                                        form.errors.expired_date
                                                    }}
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
                                                    v-model="tempLoanAmount"
                                                    id="amount"
                                                    min="0"
                                                    :placeholder="lang.enter_amount"
                                                />
                                                <div
                                                    v-if="form.errors.amount"
                                                    class="text-danger"
                                                >
                                                    {{ form.errors.amount }}
                                                </div>
                                            </div>

                                            <!-- Pay From Select Start -->
                                            <div class="col-12">
                                                <div
                                                    class="form-check form-check-inline"
                                                >
                                                    <input
                                                        class="form-check-input"
                                                        type="radio"
                                                        name="pay_from"
                                                        id="cash"
                                                        @change="
                                                            onChangePaymentMethod(
                                                                'cash'
                                                            )
                                                        "
                                                        v-model="
                                                            form.payment_method
                                                        "
                                                        value="cash"
                                                    />
                                                    <label
                                                        class="form-check-label"
                                                        for="cash"
                                                        >{{ lang.cash }}</label
                                                    >
                                                </div>
                                                <div
                                                    class=" form-check form-check-inline"
                                                >
                                                    <input
                                                        class="form-check-input"
                                                        type="radio"
                                                        name="pay_from"
                                                        id="bank"
                                                        @change="
                                                            onChangePaymentMethod(
                                                                'bank'
                                                            )
                                                        "
                                                        v-model="
                                                            form.payment_method
                                                        "
                                                        value="bank_account"
                                                    />
                                                    <label
                                                        class="form-check-label"
                                                        for="bank"
                                                        >{{ lang.bank }}</label
                                                    >
                                                </div>

                                                <div
                                                    v-if="
                                                        form.errors
                                                            .payment_method
                                                    "
                                                    class="text-danger"
                                                >
                                                    {{
                                                        form.errors
                                                            .payment_method
                                                    }}
                                                </div>
                                            </div>
                                            <!-- Pay From Select End -->

                                            <!-- Cash Selection Start -->
                                            <div
                                                class="col-12"
                                                v-if="
                                                    form.payment_method ==
                                                    'cash'
                                                "
                                            >
                                                <label
                                                    for="cash"
                                                    class="form-label required"
                                                    >{{ lang.cash }}</label
                                                >
                                                <select
                                                    name="cash_id"
                                                    v-model="
                                                        form.transactionable.id
                                                    "
                                                    id="cash"
                                                    class="form-control"
                                                    required
                                                >
                                                    <option :value="null">
                                                        {{ lang.select_cash }}
                                                    </option>
                                                    <option
                                                        v-for="(
                                                            cash, cashIndex
                                                        ) in cashes"
                                                        :value="cash.id"
                                                        :key="cashIndex"
                                                        v-text="cash.title"
                                                    />
                                                </select>
                                                <div
                                                    class="text-danger"
                                                    v-if="
                                                        form.errors[
                                                            'transactionable.id'
                                                        ]
                                                    "
                                                >
                                                    {{
                                                        form.errors[
                                                            "transactionable.id"
                                                        ].replace(
                                                            "transactionable.id",
                                                            "cash"
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                            <!-- Cash Selection End -->

                                            <!-- Bank Account Selection Start -->
                                            <div
                                                class="col-12"
                                                v-if="
                                                    form.payment_method ==
                                                    'bank_account'
                                                "
                                            >
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label
                                                            for="bank"
                                                            class=" form-label required"
                                                            >{{ lang.bank }}</label
                                                        >
                                                        <select
                                                            name="cash_id"
                                                            v-model="
                                                                selected_bank
                                                            "
                                                            id="bank"
                                                            class="form-control"
                                                            required
                                                        >
                                                            <option
                                                                :value="null"
                                                                disabled
                                                            >
                                                                {{ lang.select_bank }}
                                                            </option>
                                                            <option
                                                                v-for="(
                                                                    bank,
                                                                    bankIndex
                                                                ) in banks"
                                                                :value="bank"
                                                                :key="bankIndex"
                                                                v-text="
                                                                    bank.name
                                                                "
                                                            />
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            for="bank-account"
                                                            class=" form-label required"
                                                            >{{ lang.bank_account }}</label
                                                        >
                                                        <select
                                                            name="cash_id"
                                                            v-model="
                                                                form
                                                                    .transactionable
                                                                    .id
                                                            "
                                                            id="bank-account"
                                                            class="form-control"
                                                            required
                                                        >
                                                            <option
                                                                :value="null"
                                                                disabled
                                                            >
                                                               {{ lang.select_bank_account}}
                                                            </option>
                                                            <option
                                                                v-for="(
                                                                    bankAccount,
                                                                    bankAccountIndex
                                                                ) in bankAccounts"
                                                                :value="
                                                                    bankAccount.id
                                                                "
                                                                :key="
                                                                    bankAccountIndex
                                                                "
                                                                v-text="
                                                                    bankAccount.account_name
                                                                "
                                                            />
                                                        </select>
                                                        <div
                                                            class="text-danger"
                                                            v-if="
                                                                form.errors[
                                                                    'transactionable.id'
                                                                ]
                                                            "
                                                        >
                                                            {{
                                                                form.errors[
                                                                    "transactionable.id"
                                                                ].replace(
                                                                    "transactionable.id",
                                                                    "bank account"
                                                                )
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Bank Account Selection End -->

                                            <div
                                                class="col-12"
                                                v-if="currentBalance"
                                            >
                                                <p
                                                    class="px-2 text-white  bg-secondary"
                                                    v-text="currentBalance"
                                                />
                                            </div>

                                            <div class="col-12">
                                                <label
                                                    for="note"
                                                    class="form-label"
                                                    >{{ lang.note }}</label
                                                >
                                                <textarea
                                                    class="form-control"
                                                    v-model="form.note"
                                                    id="note"
                                                    :placeholder="lang.enter_note"
                                                ></textarea>
                                                <div
                                                    v-if="form.errors.note"
                                                    class="text-danger"
                                                >
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
                                            data-bs-dismiss="modal"
                                        >
                                          {{ lang.close }}
                                        </button>
                                        <button
                                            type="submit"
                                            class="btn custom-btn btn-success"
                                            :disabled="form.processing"
                                        >
                                            <span
                                                v-if="form.processing"
                                                class=" spinner-border spinner-border-sm"
                                                role="status"
                                                aria-hidden="true"
                                            ></span>
                                           {{lang.save}}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End New Category Modal -->

                    <!-- content body -->
                    <div class="p-0 card-body">
                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" width="100">{{ lang.sl }}</th>
                                        <th scope="col">{{ lang.loan_account }}</th>
                                        <th scope="col">{{ lang.date }}</th>
                                        <th scope="col">{{ lang.amount }}</th>
                                        <th scope="col">{{ lang.paid }}</th>
                                        <th scope="col">{{ lang.adjustment }}</th>
                                        <th scope="col">{{ lang.due }}</th>
                                        <th scope="col">{{ lang.expired_date }}</th>
                                        <th scope="col" width="100">{{ lang.action }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, index) in loans"
                                        :key="index"
                                    >
                                        <th scope="row">{{ index + 1 }}</th>
                                        <td>{{ item.loan_account_name }}</td>
                                        <td>{{ item.date }}</td>
                                        <td>
                                            {{ Math.abs(item.amount) }}

                                            <span
                                                :class="[
                                                    item.amount < 0
                                                        ? 'text-success'
                                                        : 'text-danger',
                                                ]"
                                            >
                                                ({{
                                                    item.amount < 0
                                                        ? "Rec"
                                                        : "Pay"
                                                }})
                                            </span>
                                        </td>
                                        <td>{{ Math.abs(item.paid) }}</td>
                                        <td>{{ Math.abs(item.adjustment) }}</td>
                                        <td>
                                            {{ Math.abs(item.due) }}
                                            <span
                                                :class="[
                                                    item.due <= 0
                                                        ? 'text-success'
                                                        : 'text-danger',
                                                ]"
                                            >
                                                ({{
                                                    item.due <= 0
                                                        ? "Rec"
                                                        : "Pay"
                                                }})
                                            </span>
                                        </td>
                                        <td>
                                            {{ item.expired_date }}
                                        </td>
                                        <td>
                                            <show-icon :Url="route('loan.show', item.id)" />
                                            <edit-modal-icon @click.prevent="editLoan(item)" />

                                            <delete-icon @click.prevent="deleteLoan(item.id)"
                                                         :class="{disabled:
                                                        item.loan_installments_count >0,}"
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import Button from "@/Components/Button";
import "vue-select/dist/vue-select.css";
import vSelect from "vue-select";
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import ShowIcon from "@/Pages/ReusableComponent/Icon/ShowIcon";
import EditModalIcon from "@/Pages/ReusableComponent/Icon/EditModalIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";

export default {
    components: {
        Button,
        BreezeAuthenticatedLayout,
        vSelect,
        TopBar,
        ShowIcon,
        EditModalIcon,
        DeleteIcon,
    },
    props: {
        loanAccounts: Object,
        loans: Object,
        cashes: Object,
        banks: Object,
        showrooms: Object,
    },
    computed: {
        bankAccounts() {
            try {
                return this.selected_bank.bank_accounts;
            } catch (error) {
                return [];
            }
        },
        currentBalance() {
            try {
                if (this.form.payment_method == "cash") {
                    return (
                        `Cash balance BDT ` +
                        this.cashes.find(
                            (cash) => cash.id === this.form.transactionable.id
                        ).balance
                    );
                } else if (this.form.payment_method == "bank_account") {
                    return (
                        `Bank account balance BDT ` +
                        this.selected_bank.bank_accounts.find(
                            (bankAccount) =>
                                bankAccount.id == this.form.transactionable.id
                        ).balance
                    );
                } else {
                    return null;
                }
            } catch (error) {
                return null;
            }
        },
    },
    data() {
        return {
            lang:this.$page.props.lang,
            modal: {},
            form: this.$inertia.form({
                loan_account_id: null,
                showroom_id: null,
                date: new Date().toISOString().substr(0, 10),
                amount: null,
                expired_date: null,
                note: null,
                payment_method: "cash", // cash or bank_account
                transactionable: {
                    id: null,
                },
                selected_bank: null,
            }),
            loanType: "give",
            tempLoanAmount: null,
            selected_bank: null,
        };
    },
    methods: {
        showModal() {
            this.modal.show();
        },

        hideModal() {
            this.modal.hide();
        },

        resetModal() {
            this.form = this.$inertia.form({
                loan_account_id: null,
                showroom_id: null,
                date: null,
                amount: null,
                expired_date: null,
                note: null,
                payment_method: "cash", // cash or bank_account
                transactionable: {
                    id: null,
                },
                selected_bank: null,
            });
            this.loanType = "give";
            this.tempLoanAmount = null;
        },

        submitForm() {
            if (this.loanType == "give") {
                this.form.amount = -1 * this.tempLoanAmount;
            } else {
                this.form.amount = this.tempLoanAmount;
            }

            if (this.form.id) {
                this.updateLoan();
                return;
            }

            this.form.post(this.route("loan.store"), {
                preserveScroll: true,
                onSuccess: () => this.hideModal(),
            });
        },
        deleteLoan(item_id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("loan/" + item_id, {
                        onSuccess: () => {
                            toast.fire({
                                icon: 'success',
                                title: 'Successfully Deleted!'
                            })
                        },
                    });
                }
            })
        },
        editLoan(loan) {
            if (loan.amount > 0) {
                this.loanType = "take";
            }
            this.tempLoanAmount = Math.abs(loan.amount);

            this.form = this.$inertia.form({
                ...loan,
            });

            if (loan.payment_method == "bank_account") {
                this.selected_bank = this.banks.find((bank) =>
                    bank.bank_accounts.find(
                        (bank_account) =>
                            bank_account.id === loan.transactionable_id
                    )
                );
            }

            this.showModal();
        },
        updateLoan() {
            this.form.patch(this.route("loan.update", this.form.id), {
                preserveScroll: true,
                onSuccess: () => {
                    this.hideModal()
                    alert().fire({
                        icon: 'success',
                        title: 'Successfully Updated!'
                    })
                },
            });
        },
        onChangePaymentMethod(currentMethod) {
            if (currentMethod == "cash") {
                if (this.form.id) {
                    return;
                }
                this.selectFirstCash();
            } else {
                this.selected_bank = null;
                this.form.transactionable.id = null;
            }
        },
        selectFirstCash() {
            try {
                this.form.transactionable.id = this.cashes[0].id;
            } catch (error) {
                alert("Failed to select first cash. Try again later");
            }
        },
        onClickNewEntry() {
            this.selectFirstCash();
            this.showModal();
        },
    },

    mounted() {
        this.modal = new bootstrap.Modal(this.$refs.modal);
        this.$refs.modal.addEventListener("hidden.bs.modal", () =>
            this.resetModal()
        );
    },
};
</script>
