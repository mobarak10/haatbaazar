<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('unit.index')" :modal="showModal" />
            <div class="unit container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card-header p-0 py-1 d-sm-flex d-block  align-items-center border-0">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_unit }}</h4><br>
                        </div>
                        <!-- add -->

                        <a class="btn top-icon-button print-none ms-auto" :title="lang.create_new" @click="showModal">
                            <i class="bi bi-plus-circle"></i>
                        </a>
                    </div>


                    <!-- New Category Modal -->
                    <div class="modal fade" id="payment-method" ref="modal" tabindex="-1" aria-labelledby="paymentMethodLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="#" @submit.prevent="submitForm()">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="paymentMethodLabel">{{ lang.add_new_unit }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label for="name" class="form-label required">{{ lang.unit_name }}</label>
                                                <input type="text" class="form-control" required v-model="form.name" id="name" placeholder="Ex: Mon">
                                                <div v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</div>
                                            </div>

                                            <div class="col-6">
                                                <label for="label" class="form-label required">{{ lang.labels }}</label>
                                                <input type="text" class="form-control" required v-model="form.label" id="label" placeholder="Ex: Mon/Kg">
                                                <div v-if="form.errors.label" class="text-danger">{{ form.errors.label }}</div>
                                            </div>

                                            <div class="col-6">
                                                <label for="relation" class="form-label required">{{ lang.relation }}</label>
                                                <input type="text" required class="form-control" v-model="form.relation" id="relation" placeholder="Ex: 1/40">
                                                <div v-if="form.errors.relation" class="text-danger">{{ form.errors.relation }}</div>
                                            </div>

                                            <div class="col-12">
                                                <label for="description" class="form-label required">{{ lang.description }}</label>
                                                <textarea v-model="form.description" placeholder="Ex:(1 Box = 50 Kg)" required="required" id="description" class="form-control"></textarea>
                                                <div v-if="form.errors.description" class="text-danger">{{ form.errors.description }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn custom-btn btn-danger"
                                                data-bs-dismiss="modal">{{ lang.close }}</button>
                                        <button type="submit" class="btn custom-btn btn-success">{{ lang.save }}</button>
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
                                    <th scope="col" width="100">{{lang.sl}}</th>
                                    <th scope="col">{{ lang.unit_name }}</th>
                                    <th scope="col">{{ lang.labels }}</th>
                                    <th scope="col">{{ lang.relation }}</th>
                                    <th scope="col">{{ lang.description }}</th>
                                     <th scope="col" class="text-end" width="100">{{ lang.action }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(unit, index) in units" :key="index">
                                    <th scope="row">{{ index + 1 }}</th>
                                    <td>{{ unit.name }}</td>
                                    <td>{{ unit.label }}</td>
                                    <td>{{ unit.relation }}</td>
                                    <td>{{ unit.description }}</td>
                                    <td class="text-end">
                                        <edit-modal-icon @click.prevent="editUnit(unit)" />
                                        <delete-icon  @click.prevent="deleteUnit(unit.id)" />
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
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import EditModalIcon from "@/Pages/ReusableComponent/Icon/EditModalIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";

export default {
    name: "Index",
    props: {
        units: Object,
    },
    data(){
        return {
            lang: this.$page.props.lang,
            modal: {},
            form: this.$inertia.form({
                state: 'create',
                name: null,
                label: null,
                relation: null,
                description: null,
            }),
        }
    },
    components:{
        BreezeAuthenticatedLayout,
        TopBar,
        CreateIcon,
        EditModalIcon,
        DeleteIcon,
    },

    methods: {
        showModal(){
            this.modal.show()
        },

        hideModal(){
            this.modal.hide()
        },

        resetModal(){
            this.form.name = null
        },

        editUnit(unit) {
            this.form = this.$inertia.form({
                state: 'update',
                ...unit,
            });
            this.showModal();
        },

        deleteUnit(id) {
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("unit/" + id, {
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

        submitForm(){
            if (this.form.state === 'create') {
                this.form.post(this.route('unit.store'), {
                    preserveScroll: true,
                    onSuccess: () => this.hideModal()
                })
            }else{
                this.form.patch(this.route('unit.update', this.form.id), {
                    preserveScroll: true,
                    onSuccess: () => this.hideModal()
                })
            }
        },
    },

    mounted() {
        this.modal = new bootstrap.Modal(this.$refs.modal)
        this.$refs.modal.addEventListener('hidden.bs.modal', () => this.resetModal())
    }
}
</script>

<style scoped>

</style>
