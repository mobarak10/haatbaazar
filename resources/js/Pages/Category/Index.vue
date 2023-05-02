<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('category.index')" :modal="showModal" />

            <div class="container sale">
                <!-- table -->
                <div class="border-0 card">
                    <div class="p-0 py-1 border-0 card-header d-sm-flex d-block align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_categories }}</h4><br>
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->

                        <button type="button" role="button" class="btn top-icon-button print-none ms-auto" :title="lang.create_new_category" @click="showModal">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>

                    <!-- New Category Modal -->
                    <div class="modal fade" id="payment-method" ref="modal" tabindex="-1" aria-labelledby="paymentMethodLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="#" @submit.prevent="submitForm()">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="paymentMethodLabel">{{ lang.add_new_category }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label for="name" class="form-label required">{{ lang.category }}</label>
                                                <input type="text" class="form-control" v-model="form.name" id="name" :placeholder=" lang.enter_category_name ">
                                                <div v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</div>
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
                    <div class="p-0 card-body">
                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col" width="100">{{ lang.sl }}</th>
                                    <th scope="col">{{ lang.category }}</th>
                                    <th scope="col" class="text-end" width="100">{{ lang.action }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item,index) in categories" :key="index">
                                    <th scope="row">{{ index + 1 }}</th>
                                    <td>{{ item.name }}</td>
                                    <td class="text-end">
                                        <edit-modal-icon @click.prevent="editCategory(item)" />
                                        <delete-icon @click.prevent="deleteCategory(item)" :class="[item.product_name ? 'disabled' : '']" />
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
import Button from "@/Components/Button";
import alert from "@/alert";
import deleteAlert from "@/deleteAlert";
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
        categories:Object
    },
    data(){
        return {
            lang: this.$page.props.lang,
            modal: {},
            form: this.$inertia.form({
                name: '',
            }),
        }
    },
    methods:{
        showModal(){
            this.modal.show()
        },

        hideModal(){
            this.modal.hide()
        },

        resetModal(){
            this.form.name = null
        },

        editCategory(item) {
            this.form = this.$inertia.form({
                ...item,
            });
            this.showModal();
        },

        submitForm(){
            if (this.form.id) {
                this.updateCategory();
                return;
            }

            this.form.post(this.route('category.store'), {
                preserveScroll: true,
                onSuccess: () => this.hideModal()
            })
        },

        updateCategory() {
            let toast = alert()
            this.form.patch(this.route("category.update", this.form.id), {
                preserveScroll: true,
                onSuccess: (page) => {
                    this.hideModal()
                    toast.fire({
                        icon: 'success',
                        title: 'Successfully Updated!'
                    })
                }
            });
        },
        deleteCategory(item){
            let toast = alert()
            if(item.product_name) {
                toast.fire({
                    icon: 'warning',
                    title: 'Category has product, so it can\'t be deleted!'
                })
                return
            }
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("category/" + item.id, {
                        onSuccess: () => {
                            toast.fire({
                                icon: 'success',
                                title: 'Successfully Deleted!'
                            })
                        },
                    });
                }
            })
        }
    },

    mounted() {
        this.modal = new bootstrap.Modal(this.$refs.modal)
        this.$refs.modal.addEventListener('hidden.bs.modal', () => this.resetModal())
    }

}
</script>
