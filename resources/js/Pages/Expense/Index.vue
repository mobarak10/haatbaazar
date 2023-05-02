<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('expense.index')" :modal="onClickNewEntry" />
            <div class="container sale">
                <!-- table -->
                <div class="border-0 card">
                    <div
                        class="p-0 py-1 border-0  card-header d-sm-flex d-block align-items-center"
                    >
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_expense }}</h4>
                            <br />
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->

                        <button
                            type="button"
                            role="button"
                            class="btn top-icon-button print-none ms-auto"
                            :title="lang.create_new_expense"
                            @click="showModal"
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
                        data-bs-backdrop="static"
                        aria-labelledby="paymentMethodLabel"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog modal-xl">
                            <form action="#" @submit.prevent="submitForm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5
                                            class="modal-title"
                                            id="paymentMethodLabel"
                                        >
                                            {{ lang.new_expense_entry }}
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
                                                    for="showroom"
                                                    class="form-label required"
                                                >{{ lang.showroom }}</label
                                                >
                                                <select
                                                    name="showroom"
                                                    v-model="form.showroom_id"
                                                    id="showroom"
                                                    class="form-control"
                                                >
                                                    <option
                                                        :value="null"
                                                        disabled
                                                    >
                                                        {{ lang.select_showroom }}
                                                    </option>
                                                    <option
                                                        v-for="(
                                                            showroom,
                                                            showroomIndex
                                                        ) in showrooms"
                                                        :value="showroom.id"
                                                        :key="showroomIndex"
                                                        v-text="
                                                            showroom.name
                                                        "
                                                    />
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label
                                                    for="cost-category"
                                                    class="form-label required"
                                                    >{{ lang.cost_category }}</label
                                                >
                                                <select
                                                    name="cost_category_id"
                                                    v-model="cost_category_id"
                                                    id="cost-category"
                                                    class="form-control"
                                                    @change="
                                                        form.cost_subcategory_id =
                                                            null
                                                    "
                                                >
                                                    <option
                                                        :value="null"
                                                        disabled
                                                    >
                                                        {{ lang.select_cost_category }}
                                                    </option>
                                                    <option
                                                        v-for="(
                                                            costCategory,
                                                            costCategoryIndex
                                                        ) in costCategories"
                                                        :value="costCategory.id"
                                                        :key="costCategoryIndex"
                                                        v-text="
                                                            costCategory.name
                                                        "
                                                    />
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label
                                                    for="cost-subcategory"
                                                    class="form-label required"
                                                    >{{ lang.cost_subcategory }}</label
                                                >
                                                <select
                                                    name="cost_subcategory_id"
                                                    v-model="
                                                        form.cost_subcategory_id
                                                    "
                                                    id="cost-subcategory"
                                                    class="form-control"
                                                >
                                                    <option
                                                        :value="null"
                                                        disabled
                                                    >
                                                       {{ lang.select_cost_subcategory}}
                                                    </option>
                                                    <option
                                                        v-for="(
                                                            costSubcategory,
                                                            costSubcategoryIndex
                                                        ) in costSubCategories"
                                                        :value="
                                                            costSubcategory.id
                                                        "
                                                        :key="
                                                            costSubcategoryIndex
                                                        "
                                                        v-text="
                                                            costSubcategory.name
                                                        "
                                                    />
                                                </select>
                                                <div
                                                    v-if="
                                                        form.errors
                                                            .cost_subcategory_id
                                                    "
                                                    class="text-danger"
                                                >
                                                    {{
                                                        form.errors
                                                            .cost_subcategory_id
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
                                                    for="amount"
                                                    class="form-label required"
                                                    >{{  lang.amount  }}</label
                                                >
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    v-model="form.amount"
                                                    id="amount"
                                                    min="0"
                                                    step="any"
                                                    :placeholder="lang.enter_expense_amount"
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
                                                               {{lang.select_bank_account}}
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
                                                    for="description"
                                                    class="form-label"
                                                    >{{ lang.description }}</label
                                                >
                                                <textarea
                                                    class="form-control"
                                                    v-model="form.description"
                                                    id="description"
                                                    :placeholder="lang.enter_description"
                                                ></textarea>
                                                <div
                                                    v-if="
                                                        form.errors.description
                                                    "
                                                    class="text-danger"
                                                >
                                                    {{
                                                        form.errors.description
                                                    }}
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
                                           {{ lang.close}}
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
                                        <th scope="col">{{ lang.cost_subcategory }}</th>
                                        <th scope="col">{{ lang.date }}</th>
                                        <th scope="col">{{ lang.amount }}</th>
                                        <th scope="col">{{ lang.description }}</th>
                                        <th scope="col" width="100">{{lang.action}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, index) in expenses.data"
                                        :key="index"
                                    >
                                        <th scope="row">
                                            {{ expenses.from + index }}
                                        </th>
                                        <td>
                                            {{ item.cost_subcategory_name }}
                                        </td>
                                        <td>{{ formatter.dateFormat(item.date) }}</td>
                                        <td>{{ item.amount }}</td>
                                        <td>{{ item.description }}</td>
                                        <td>
                                            <edit-modal-icon  @click.prevent="editExpense(item)" />
                                            <delete-icon  @click.prevent="deleteExpense(item.id)" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="expenses.links" />
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import Button from "@/Components/Button";
import Pagination from "@/Components/Pagination";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import EditModalIcon from "@/Pages/ReusableComponent/Icon/EditModalIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import * as _formatter from "@/Helpers/formatter";

export default {
    components: {
        Button,
        BreezeAuthenticatedLayout,
        Pagination,
        TopBar,
        EditModalIcon,
        DeleteIcon,
    },
    props: {
        costCategories: Object,
        showrooms: Object,
        expenses: Object,
        cashes: Object,
        banks: Object,
    },
    computed: {
        formatter() {
            return _formatter
        },

        costSubCategories() {
            if (!this.cost_category_id) {
                return [];
            }
            return this.costCategories.find(
                (costCategory) => costCategory.id == this.cost_category_id
            ).cost_subcategories;
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
                cost_subcategory_id: null,
                showroom_id: null,
                date: new Date().toISOString().substr(0, 10),
                amount: null,
                description: "",
                payment_method: "cash", // cash or bank_account
                transactionable: {
                    id: null,
                },
            }),
            cost_category_id: null,
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
                cost_subcategory_id: null,
                showroom_id: null,
                date: new Date().toISOString().substr(0, 10),
                amount: null,
                description: "",

                payment_method: "cash", // cash or bank_account
                transactionable: {
                    id: null,
                },
            });
            this.cost_category_id = null;
        },

        submitForm() {
            if (this.form.id) {
                this.udpateExpense();
                return;
            }
            this.form.post(this.route("expense.store"), {
                preserveScroll: true,
                onSuccess: () => this.hideModal(),
            });
        },
        deleteExpense(item_id) {
            if (window.confirm("Are you sure to delete this record")) {
                this.$inertia.delete("expense/" + item_id);
            }
        },
        editExpense(expense) {
            this.selectCostCategoryByCostSubcategoryId(
                expense.cost_subcategory_id
            );
            this.form = this.$inertia.form({
                ...expense,
            });

            if (expense.payment_method == "bank_account") {
                this.selected_bank = this.banks.find((bank) =>
                    bank.bank_accounts.find(
                        (bank_account) =>
                            bank_account.id === expense.transactionable_id
                    )
                );
            }
            this.showModal();
        },
        udpateExpense() {
            this.form.patch(this.route("expense.update", this.form.id), {
                preserveScroll: true,
                onSuccess: () => this.hideModal(),
            });
        },
        selectCostCategoryByCostSubcategoryId(costSubCategoryId) {
            try {
                this.cost_category_id = this.costCategories.find(
                    (costCategory) =>
                        costCategory.cost_subcategories.find(
                            (costSubCategory) =>
                                costSubCategory.id == costSubCategoryId
                        )
                ).id;
            } catch (error) {
                this.resetModal();
                alert("Something is went wrong. Refresh or try again later");
            }
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
        init() {
            this.selectFirstCash();
        },
    },

    mounted() {
        this.init();
        this.modal = new bootstrap.Modal(this.$refs.modal);
        this.$refs.modal.addEventListener("hidden.bs.modal", () =>
            this.resetModal()
        );
    },
};
</script>
