<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('mokam.index')" :modal="showModal" />

            <div class="container sale">
                <!-- table -->
                <div class="border-0 card">
                    <div
                        class="p-0 py-1 border-0  card-header d-sm-flex d-block align-items-center"
                    >
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_mokam }}</h4>
                            <br />
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->

                        <button
                            type="button"
                            role="button"
                            class="btn top-icon-button print-none ms-auto"
                            :title="lang.create_new_mokam"
                            @click="showModal"
                        >
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>

                    <!-- New Category Modal -->
                    <div
                        class="modal fade"
                        id="mokam"
                        ref="modal"
                        tabindex="-1"
                        aria-labelledby="mokamLabel"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog">
                            <form action="#" @submit.prevent="submitForm()">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5
                                            class="modal-title"
                                            id="mokamLabel"
                                        >
                                            {{ lang.add_new_mokam }}
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
                                                    :placeholder="lang.enter_mokam_name"
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
                                    <th scope="col">{{ lang.name }}</th>
                                    <th scope="col">{{ lang.description }}</th>
                                    <th scope="col" width="100">{{ lang.action }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr
                                    v-for="(item, index) in mokams"
                                    :key="index"
                                >
                                    <th scope="row">{{ index + 1 }}</th>
                                    <td>{{ item.name }}</td>
                                    <td>{{ item.description }}</td>
                                    <td>
                                        <edit-modal-icon @click.prevent="editMokam(item)" />
                                        <delete-icon @click.prevent="deleteMokam(item.id)" />
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
        mokams: Object,
    },
    data() {
        return {
            lang:this.$page.props.lang,
            modal: {},
            form: this.$inertia.form({
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
                name: "",
                description: "",
            });
        },

        submitForm() {
            if (this.form.id) {
                this.updateMokam();
                return;
            }

            this.form.post(this.route("mokam.store"), {
                preserveScroll: true,
                onSuccess: () => this.hideModal(),
            });
        },
        deleteMokam(item_id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("mokam/" + item_id, {
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
        editMokam(mokam) {
            this.form = this.$inertia.form({
                ...mokam,
            });
            this.showModal();
        },
        updateMokam() {
            this.form.patch(this.route("mokam.update", this.form.id), {
                preserveScroll: true,
                onSuccess: () => this.hideModal(),
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
