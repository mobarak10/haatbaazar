<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('income-sector.index')" :modal="showModal" />

            <div class="container sale">
                <!-- table -->
                <div class="border-0 card">
                    <div
                        class="p-0 py-1 border-0  card-header d-sm-flex d-block align-items-center"
                    >
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_income_sector }}</h4>
                            <br />
                        </div>
                        <!-- add -->

                        <button
                            type="button"
                            role="button"
                            class="btn top-icon-button print-none ms-auto"
                            :title="lang.create_new_income_sector"
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
                                            {{ lang.add_new_income_sector }}
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
                                                    for="name"
                                                    class="form-label required"
                                                >{{ lang.name }}</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    v-model="form.name"
                                                    id="name"
                                                    :placeholder="lang.enter_income_sector_name"
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
                                                    for="address"
                                                    class="form-label"
                                                >{{ lang.description }}</label
                                                >
                                                <textarea
                                                    class="form-control"
                                                    v-model="form.description"
                                                    id="address"
                                                    :placeholder="lang.enter_address"
                                                ></textarea>
                                                <div
                                                    v-if="form.errors.description"
                                                    class="text-danger"
                                                >
                                                    {{ form.errors.description }}
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
                                    <th scope="col">{{ lang.name }}</th>
                                    <th scope="col">{{ lang.description }}</th>
                                    <th scope="col" width="100">{{ lang.action }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- Starts from here -->
                                <tr
                                    v-for="(sector, index) in income_sectors"
                                    :key="index"
                                >
                                    <th scope="row">{{ index + 1 }}</th>
                                    <td>{{ sector.name }}</td>
                                    <td>{{ sector.description }}</td>
                                    <td>
                                        <edit-modal-icon @click.prevent="editIncomeSector(sector)" />
                                        <delete-icon @click.prevent="deleteIncomeSector(sector.id)" />
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
import Button from "@/Components/Button";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import EditModalIcon from "@/Pages/ReusableComponent/Icon/EditModalIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";

export default {
    name: "Index",
    components: {
        Button,
        BreezeAuthenticatedLayout,
        TopBar,
        EditModalIcon,
        DeleteIcon,
    },
    props: {
        income_sectors: Object,
    },
    data() {
        return {
            lang:this.$page.props.lang,
            modal: {},
            form: this.$inertia.form({
                name: null,
                description: null,
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
                name: null,
                description: null,
            });
        },

        submitForm() {
            if (this.form.id) {
                this.updateIncomeSector();
                return;
            }

            this.form.post(this.route("income-sector.store"), {
                preserveScroll: true,
                onSuccess: () => this.hideModal(),
            });
        },
        deleteIncomeSector(sector_id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("income-sector/" + sector_id, {
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
        editIncomeSector(sector) {
            this.form = this.$inertia.form({
                ...sector,
            });
            this.showModal();
        },
        updateIncomeSector() {
            this.form.patch(this.route("income-sector.update", this.form.id), {
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
    },

    mounted() {
        this.modal = new bootstrap.Modal(this.$refs.modal);
        this.$refs.modal.addEventListener("hidden.bs.modal", () =>
            this.resetModal()
        );
    },
}
</script>

<style scoped>

</style>
