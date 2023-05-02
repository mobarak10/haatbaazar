<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('user.index')" :create-route="route('user.create')" />
            <div class="sale container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card-header p-0 py-1 d-sm-flex d-block  align-items-center border-0">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.all_products }}</h4><br>
                            <!-- <small class="print-none">About 563,000,000 results (0.98 seconds)</small> -->
                        </div>
                        <!-- add -->
                        <create-icon :Url="route('user.create')" />
                    </div>

                    <!-- content body -->
                    <div class="card-body p-0">

                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col" width="100">{{ lang.sl }}</th>
                                    <th scope="col">{{ lang.name }}</th>
                                    <th scope="col">{{ lang.email }}</th>
                                    <th scope="col">{{ lang.role }}</th>
                                    <th scope="col" width="100">{{ lang.action }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item,index) in users" :key="index">
                                    <th scope="row">{{ index + 1 }}</th>
                                    <td>{{ item.name }}</td>
                                    <td>{{ item.email }}</td>
                                    <td><span v-for="(role) in item.roles">{{ role.name +',' }}</span></td>
                                    <td>
                                        <edit-icon :Url="route('user.edit', item.id)" />
                                        <delete-icon @click.prevent="deleteUser(item.id)" />
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
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import CreateIcon from "@/Pages/ReusableComponent/Icon/CreateIcon";
import EditIcon from "@/Pages/ReusableComponent/Icon/EditIcon";
import DeleteIcon from "@/Pages/ReusableComponent/Icon/DeleteIcon";
export default {
    data(){
        return{
            lang:this.$page.props.lang,
        }
    },
    name: "Index",
    components: {
        BreezeAuthenticatedLayout,
        TopBar,
        CreateIcon,
        EditIcon,
        DeleteIcon,
    },
    props: {
        users: Object,
    },
    methods: {
        deleteUser(id) {
            if (window.confirm('are you sure to delete this record')){
                this.$inertia.delete("user/" + id);
            }
        },
    },
    mounted() {
    }
}
</script>

<style scoped>

</style>



