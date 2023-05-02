<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('showroom.index')" :create-route="route('showroom.create')" />

            <div class="sale container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card-header p-0 py-1 d-sm-flex d-block  align-items-center border-0">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_showroom }}</h4><br>
                        </div>
                        <!-- add -->
                        <create-icon :Url="route('showroom.create')" />
                    </div>

                    <!-- content body -->
                    <div class="card-body p-0">

                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" width="100">{{lang.sl}}</th>
                                        <th scope="col">{{ lang.name }}</th>
                                        <th scope="col">{{ lang.address }}</th>
                                        <th scope="col">{{ lang.description }}</th>
                                        <th scope="col" width="100">{{ lang.action }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in showrooms" :key="index">
                                        <th scope="row">{{ index + 1 }}</th>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.address }}</td>
                                        <td>{{ item.comment }}</td>
                                        <td>
                                            <edit-icon :Url="route('showroom.edit', item.id)" />
                                            <delete-icon @click.prevent="deleteShowroom(item.id)" />
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
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";

export default {
    data(){
        return{
               lang: this.$page.props.lang,
        }
    },
    components: {
        Button,
        BreezeAuthenticatedLayout,
        TopBar,
        CreateIcon,
        EditIcon,
        DeleteIcon,
    },
    props: {
        showrooms:Object,
        message:Text
    },

    methods:{
        deleteShowroom(item_id){
            let toast = alert()
            deleteAlert().then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete("showroom/" + item_id, {
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
    },

    mounted() {

    }

}
</script>
