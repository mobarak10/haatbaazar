<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container print-none">
                <ul class="mt-2 nav nav-tabs print-none">
                    <li class="nav-item">
                        <a
                            class="nav-link active"
                            :href="route('cost-category.index')"
                            >{{ lang.all_records }}</a
                        >
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" @click="showModal">{{ lang.new_entry }}</a>
                    </li>
                </ul>
            </div>

            <div class="container sale">
                <!-- table -->
                <div class="border-0 card">
                    <div
                        class="p-0 py-1 border-0  card-header d-sm-flex d-block align-items-center"
                    >
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_cost_sub_categories }}</h4>
                            <br />
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->

                        <button
                            type="button"
                            role="button"
                            class="btn top-icon-button print-none ms-auto"
                            :title="lang.create_new_cost_subcategory"
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
                                            {{ lang.add_new_cost_subcategory }}
                                        </h5>
                                        <button
                                            type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"
                                            :disabled="form.processing"
                                            aria-label="Close"
                                        ></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label
                                                    for="cost-category"
                                                    class="form-label required"
                                                    >{{ lang.cost_category }}</label
                                                >
                                                <select
                                                    name="cost_category_id"
                                                    v-model="
                                                        form.cost_category_id
                                                    "
                                                    id="cost-category"
                                                    class="form-control"
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
                                                <div
                                                    v-if="
                                                        form.errors
                                                            .cost_category_id
                                                    "
                                                    class="text-danger"
                                                >
                                                    {{
                                                        form.errors
                                                            .cost_category_id
                                                    }}
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label
                                                    for="name"
                                                    class="form-label required"
                                                    >{{ lang.name }}</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    v-model="form.name"
                                                    id="name"
                                                    :placeholder="lang.enter_cost_subcategory_name"
                                                />
                                                <div
                                                    v-if="form.errors.name"
                                                    class="text-danger"
                                                >
                                                    {{ form.errors.name }}
                                                </div>
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
                                                    v-if="form.errors.name"
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
                                            {{ lang.save }}
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
                                        <th scope="col">{{ lang.cost_category }}</th>
                                        <th scope="col">{{ lang.name }}</th>
                                        <th scope="col">{{ lang.description }}</th>
                                        <th scope="col" width="100">{{ lang.action }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            item, index
                                        ) in costSubcategories"
                                        :key="index"
                                    >
                                        <th scope="row">{{ index + 1 }}</th>
                                        <td>{{ item.cost_category_name }}</td>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.description }}</td>
                                        <td>
                                            <a
                                                href="#"
                                                @click.prevent="
                                                    editCostSubcategory(item)
                                                "
                                                class=" btn table-small-button text-warning"
                                                :title="lang.edit"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a
                                                href="#"
                                                @click.prevent="
                                                    deleteCostSubcategory(
                                                        item.id
                                                    )
                                                "
                                                class="btn table-small-button"
                                                :class="[
                                                    item.expenses_count > 0
                                                        ? 'disabled'
                                                        : '',
                                                    item.expenses_count > 0
                                                        ? 'text-secondary'
                                                        : 'text-danger',
                                                ]"
                                            >
                                                <i
                                                    class="bi bi-trash"
                                                    :title="
                                                        item.expenses_count > 0
                                                            ? `Can't delete. This subcategory has expenses.`
                                                            : lang.delete
                                                    "
                                                ></i>
                                            </a>
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
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import EditModalIcon from "@/Pages/ReusableComponent/Icon/EditModalIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";

export default {
    components: {
        Button,
        BreezeAuthenticatedLayout,
        TopBar,
        EditModalIcon,
        DeleteIcon,
    },
    props: {
        costCategories: Object,
        costSubcategories: Object,
    },
    data() {
        return {
            lang:this.$page.props.lang,
            modal: {},
            form: this.$inertia.form({
                cost_category_id: null,
                name: "",
                description: "",
            }),
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
                cost_category_id: null,
                name: "",
                description: "",
            });
        },

        submitForm() {
            if (this.form.id) {
                this.udpateCostSubcategory();
                return;
            }

            this.form.post(this.route("cost-subcategory.store"), {
                preserveScroll: true,
                onSuccess: () => this.hideModal(),
            });
        },
        deleteCostSubcategory(item_id) {
            if (window.confirm("are you sure to delete this record")) {
                this.$inertia.delete("cost-subcategory/" + item_id);
            }
        },
        editCostSubcategory(costSubCategory) {
            this.form = this.$inertia.form({
                ...costSubCategory,
            });
            this.showModal();
        },
        udpateCostSubcategory() {
            this.form.patch(
                this.route("cost-subcategory.update", this.form.id),
                {
                    preserveScroll: true,
                    onSuccess: () => this.hideModal(),
                }
            );
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
