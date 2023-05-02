<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('role.index')" :create-route="route('role.create')" />

            <div class="container mt-2">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <span>{{ lang.role_records }}</span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th scope="col" width="5%">#</th>
                                <th scope="col">{{ lang.name }}</th>
                                <th scope="col" class="text-end">{{ lang.action }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(role, index) in roles" :key="index">
                                <td>{{ index + 1 }}</td>
                                <td>{{ role.name }}</td>
                                <td class="text-end">
                                    <inertia-link :href="route('role.show', role.id)" class="btn table-small-button" :title="lang.show">
                                        <i class="bi bi-eye"></i>
                                    </inertia-link>

                                    <inertia-link :href="route('role.edit', role.id)" class="btn table-small-button" :title="lang.edit">
                                        <i class="bi bi-pencil"></i>
                                    </inertia-link>

                                    <a href="#" @click.prevent="deleteRole(role.id)" class="btn table-small-button" :title="lang.delete">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
export default {
    data(){
        return{
            lang:this.$page.props.lang,
        }
    },
    name: "Index",
    props: {
        roles: Object
    },
    components:{
        BreezeAuthenticatedLayout,
        TopBar,
    },

    methods: {
        deleteRole(id) {
            if (window.confirm('are you sure to delete this record')){
                this.$inertia.delete("role/" + id);
            }
        },
    },
}
</script>

<style scoped>

</style>
