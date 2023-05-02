<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('bank.index')" :create-route="route('bank.create')" />
            <div class="unit container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card-header p-0 py-1 d-sm-flex d-block  align-items-center border-0">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_bank }}</h4><br>
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->
                        <create-icon :Url="route('bank.create')" />
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
                            <form action="#" @submit.prevent="updateBank()">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5
                                            class="modal-title"
                                            id="paymentMethodLabel"
                                        >
                                            Edit Bank
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
                                                    placeholder="Enter Loan Account Name"
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
                                                    class="form-label required"
                                                >{{ lang.description }}</label
                                                >
                                                <textarea v-model="form.description" class="form-control" id="description"></textarea>
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
                                           {{lang.close}}
                                        </button>
                                        <save-button :processing="form.processing" />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End New Category Modal -->

                    <!-- content body -->
                    <div class="card-body p-0">
                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" width="100">{{ lang.SL}}</th>
                                        <th scope="col">{{  lang.name}}</th>
                                        <th scope="col">{{  lang.description}}</th>
                                        <th scope="col" width="100">{{  lang.Action}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(bank, index) in banks.data" :key="index">
                                        <th scope="row">{{ index + 1 }}</th>
                                        <td>{{ bank.name }}</td>
                                        <td>{{ bank.description ?? 'N/A' }}</td>
                                        <td>
                                            <edit-modal-icon @click.prevent="editBank(bank)" />
                                            <delete-icon :disabled="bank.bank_accounts.length > 0" @click.prevent="deleteBank(bank.id)" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="banks.links" />
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Pagination from '@/Components/Pagination'
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
import EditModalIcon from "@/Pages/ReusableComponent/Icon/EditModalIcon";
import SaveButton from "@/Pages/ReusableComponent/Button/SaveButton";
import CloseButton from "@/Pages/ReusableComponent/Button/CloseButton";
export default {
    name: "Index",
    props: {
        banks: Object,
    },
    components:{
        BreezeAuthenticatedLayout,
        Pagination,
        TopBar,
        CreateIcon,
        DeleteIcon,
        EditModalIcon,
        SaveButton,
        CloseButton,
    },
    data() {
        return{
            lang:this.$page.props.lang,
            modal: {},
            form: this.$inertia.form({
                name: null,
                description: null,
            }),
        }
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

        deleteBank(id){
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("bank/" + id, {
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

        editBank(bank) {
            this.form = this.$inertia.form({
                ...bank,
            });
            this.showModal();
        },

        updateBank() {
            this.form.patch(this.route("bank.update", this.form.id), {
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
    }
}
</script>

<style scoped>

</style>
