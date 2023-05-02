<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('unit.index')" :create-route="route('unit.create')" />
            <div class="sale container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card mb-5 border-0">
                        <div class="card-header p-0 border-0 d-flex">
                            <!-- page title -->
                            <div class="mt-3">
                                <h4 class="main-title">{{ lang.create_new_product }}</h4>
                                <p><small>{{ lang.all }} <span style="color: red">*</span> {{ lang.mark_must_be_required }}</small></p>
                            </div>

                            <!-- header icon -->
                            <back-icon :Url="route('unit.index')" />
                        </div>
                    </div>

                    <form @submit.prevent="saveUnit()" method="POST">
                        <!-- type text -->
                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="unit_name" class="form-label required mt-1">{{ lang.unit_name }}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.name" class="form-control" id="unit_name" placeholder="বস্তা">
                                <div v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="description" class="form-label required mt-1">{{ lang.description }}</label>
                            </div>

                            <div class="col-8">
                                <textarea v-model="form.description" class="form-control" id="description" rows="5" placeholder="1 বস্তা = 50 কেজি"></textarea>
                                <div v-if="form.errors.description" class="text-danger">{{ form.errors.description }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label class="form-label mt-1">&nbsp;</label>
                            </div>

                            <div class="col-10">
                                <button type="reset" class="btn btn-warning me-2">
                                    <i class="bi bi-dash-square"></i>
                                    <span class="p-1">{{ lang.reset }}</span>
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-square"></i>
                                    <span class="p-1">{{ lang.save }}</span>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Button from "@/Components/Button";
import TopBar from "@/Pages/ReusableComponent/TopBar/TopBar";
import BackIcon from "@/Pages/ReusableComponent/Icon/BackIcon";

export default {
    components: {
        Button,
        BreezeAuthenticatedLayout,
        TopBar,
        BackIcon,
    },
    data(){
        return {
            lang: this.$page.props.lang,
            form: this.$inertia.form({
                name: '',
                description: '',
                labels: '',
                relation: '',
            }),
        }
    },
    methods:{
        saveUnit(){
            this.form.post(this.route('unit.store'))
        }
    },

    mounted() {

    }

}
</script>
