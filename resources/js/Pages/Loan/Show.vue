<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container mt-2">
                <!-- Print btn -->
                <div
                    class="pb-2  print d-flex justify-content-between align-items-center"
                >
                    <h4>{{ lang.loan_details }}</h4>
                    <div>
                        <print-icon />
                        <back-icon :Url="route('loan.index')" />
                    </div>
                </div>
                <!-- End of the Print btn -->

                <!-- Loan details start -->
                <div class="card">
                    <div class="text-center card-header">
                        {{ loan.loan_account.name }}
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            {{ lang.loan_type }}: {{ loanType }}
                        </li>
                        <li class="list-group-item">{{ lang.date }}: {{ loan.date }}</li>
                        <li class="list-group-item">
                            {{ lang.amount }}: {{ Math.abs(loan.amount) }}
                        </li>
                        <li class="list-group-item">
                            {{ lang.expire_date }}: {{ loan.expired_date }}
                        </li>
                        <li class="list-group-item">
                            {{ lang.total_paid }}: {{ Math.abs(loan.paid) }}
                        </li>
                        <li class="list-group-item">
                            {{ lang.total_adjustment }}: {{ Math.abs(loan.adjustment) }}
                        </li>
                        <li class="list-group-item">
                            {{ lang.total_due }}: {{ Math.abs(loan.due) }}
                        </li>
                        <li class="list-group-item">{{ lang.note }}: {{ loan.note }}</li>
                    </ul>
                </div>
                <!-- Loan Deatils End -->

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
                                        {{ lang.add_new_loan_installment }}
                                    </h5>
                                    <button
                                        type="button"
                                        class="btn-close"
                                        :disabled="form.processing"
                                        data-bs-dismiss="modal"
                                        aria-label="Close"
                                    ></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-2">
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
                                                for="amount"
                                                class="form-label required"
                                                >{{ lang.amount }}</label
                                            >
                                            <input
                                                type="number"
                                                step="any"
                                                class="form-control"
                                                v-model="
                                                    tempLoanInstallmentAmount
                                                "
                                                id="amount"
                                                :placeholder="lang.enter_amount"
                                            />
                                            <div
                                                v-if="form.errors.amount"
                                                class="text-danger"
                                            >
                                                {{ form.errors.amount }}
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label
                                                for="adjustment"
                                                class="form-label"
                                                >{{ lang.adjustment }}</label
                                            >
                                            <input
                                                type="number"
                                                step="any"
                                                class="form-control"
                                                v-model="
                                                    tempLoanInstallmentAdjustment
                                                "
                                                id="adjustment"
                                                :placeholder="lang.enter_adjustment_amount"
                                            />
                                            <div
                                                v-if="form.errors.adjustment"
                                                class="text-danger"
                                            >
                                                {{ form.errors.adjustment }}
                                            </div>
                                        </div>

                                        <!-- Pay From Select Start -->
                                        <div class="col-12">
                                            <div
                                                class=" form-check form-check-inline"
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
                                                    form.errors.payment_method
                                                "
                                                class="text-danger"
                                            >
                                                {{ form.errors.payment_method }}
                                            </div>
                                        </div>
                                        <!-- Pay From Select End -->

                                        <!-- Cash Selection Start -->
                                        <div
                                            class="col-12"
                                            v-if="form.payment_method == 'cash'"
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
                                                        v-model="selected_bank"
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
                                                                bank, bankIndex
                                                            ) in banks"
                                                            :value="bank"
                                                            :key="bankIndex"
                                                            v-text="bank.name"
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
                                                            form.transactionable
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
                                                            {{ lang.select_bank_account }}
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
                                            <label for="note" class="form-label"
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
                                       {{lang.close}}
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
                                        {{ lang.save }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- End New Category Modal -->

                <!-- Loan Installment Start -->
                <div class="mt-5">
                    <div
                        class="text-center  display-6 d-flex justify-content-center align-items-center"
                    >
                        <h3 class="text-decoration-underline">{{ lang.installments }}</h3>
                        <button
                            class="btn top-icon-button print-none"
                            title="Add new installment"
                            type="button"
                            @click="onClickNewInstallment"
                        >
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ lang.date }}</th>
                                 <th scope="col">{{ lang.note }}</th>
                                <th class="text-end" scope="col">{{ lang.amount }}</th>
                                <th class="text-end" scope="col">{{ lang.adjustment }}</th>
                                <th class="text-end print-none" scope="col">{{ lang.action }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(
                                    loanInstallment, loanInstallmentIndex
                                ) in loan.loan_installments"
                                :key="loanInstallmentIndex"
                            >
                                <th scope="row">
                                    {{ loanInstallmentIndex + 1 }}
                                </th>
                                <td>{{ loanInstallment.date }}</td>
                                <td>{{ loanInstallment.note }}</td>
                                <td class="text-end">{{ Math.abs(loanInstallment.amount) }}</td>
                                <td class="text-end">
                                    {{ Math.abs(loanInstallment.adjustment) }}
                                </td>
                                <td class="text-end print-none">
                                    <edit-modal-icon @click.prevent="editLoanInstallment(loanInstallment)" />
                                    <delete-icon  @click.prevent="deleteLoanInstallment(loanInstallment.id)" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Loan Installment End -->
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import EditModalIcon from "@/Pages/ReusableComponent/Icon/EditModalIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import BackIcon from "@/Pages/ReusableComponent/Icon/BackIcon";
import PrintIcon from "@/Pages/ReusableComponent/Icon/PrintIcon";
export default {
    name: "show",
    props: {
        loan: Object,
        cashes: Object,
        banks: Object,
    },
    components: {
        BreezeAuthenticatedLayout,
        EditModalIcon,
        DeleteIcon,
        BackIcon,
        PrintIcon,
    },

    computed: {
        loanType() {
            return this.loan.amount > 0 ? `Take` : `Give`;
        },
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
                loan_id: this.loan.id,
                showroom_id: this.loan.showroom_id,
                date: new Date().toISOString().substr(0, 10),
                amount: null,
                adjustment: null,
                note: null,
                payment_method: "cash", // cash or bank_account
                transactionable: {
                    id: null,
                },
            }),
            tempLoanInstallmentAmount: null,
            tempLoanInstallmentAdjustment: null,
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
                loan_id: this.loan.id,
                date: null,
                amount: null,
                adjustment: null,
                note: null,
                payment_method: "cash", // cash or bank_account
                transactionable: {
                    id: null,
                },
            });
            this.tempLoanInstallmentAmount = null;
            this.selected_bank = null;
        },

        submitForm() {
            if (this.loanType == "Take") {
                this.form.amount = -this.tempLoanInstallmentAmount;
                this.form.adjustment = -this.tempLoanInstallmentAdjustment;
            } else if (this.loanType == "Give") {
                this.form.amount = this.tempLoanInstallmentAmount;
                this.form.adjustment = this.tempLoanInstallmentAdjustment;
            } else {
                alert("Failed to determine loan type. Something went wrong.");
                return;
            }

            if (this.form.id) {
                this.updateLoanInstallment();
                return;
            }

            this.form.post(this.route("loan-installment.store"), {
                preserveScroll: true,
                onSuccess: () => this.hideModal(),
            });
        },
        deleteLoanInstallment(item_id) {
            if (window.confirm("are you sure to delete this record")) {
                this.$inertia.delete(
                    this.route("loan-installment.destroy", item_id)
                );
            }
        },
        editLoanInstallment(loanInstallment) {
            if (this.loanType == "Take") {
                this.tempLoanInstallmentAmount = Math.abs(
                    loanInstallment.amount
                );
                this.tempLoanInstallmentAdjustment = Math.abs(
                    loanInstallment.adjustment
                );
            } else {
                this.tempLoanInstallmentAmount = loanInstallment.amount;
                this.tempLoanInstallmentAdjustment = loanInstallment.adjustment;
            }

            this.form = this.$inertia.form({
                ...loanInstallment,
            });

            if (loanInstallment.payment_method == "bank_account") {
                this.selected_bank = this.banks.find((bank) =>
                    bank.bank_accounts.find(
                        (bank_account) =>
                            bank_account.id ===
                            loanInstallment.transactionable_id
                    )
                );
            }

            this.showModal();
        },
        updateLoanInstallment() {
            this.form.patch(
                this.route("loan-installment.update", this.form.id),
                {
                    preserveScroll: true,
                    onSuccess: () => this.hideModal(),
                }
            );
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
        onClickNewInstallment() {
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

<style scoped></style>
