<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('showroom.index')" :create-route="route('showroom.create')" />
            <div class="sale container">
                <!-- table -->
                <div class="card border-0">
                    <div class="card mb-5 border-0">
                        <div class="card-header p-0 border-0 d-flex">
                            <!-- page title -->
                            <div class="mt-3">
                                <h4 class="main-title">{{ lang.create_new_showroom }}</h4>
                                <p><small>{{ lang.all }} <span style="color: red">*</span> {{ lang.mark_must_be_required }}</small></p>
                            </div>

                            <!-- header icon -->
                            <back-icon :Url="route('showroom.index')" />
                        </div>
                    </div>

                    <form @submit.prevent="saveShowroom()" method="POST">
                        <!-- type text -->
                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="name" class="form-label required mt-1">{{ lang.name }}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.name" class="form-control" id="name" :placeholder="lang.enter_showroom_name">
                                <div v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="address" class="form-label required mt-1">{{ lang.address }}</label>
                            </div>

                            <div class="col-6">
                                <input type="text" v-model="form.address" class="form-control" id="address" :placeholder=" lang.enter_showroom_address ">
                                <div v-if="form.errors.address" class="text-danger">{{ form.errors.address }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="comment" class="form-label mt-1">{{ lang.description }}</label>
                            </div>

                            <div class="col-6">
                                <textarea v-model="form.comment" id="comment" rows="6" class="form-control" :placeholder="lang.write_something_about_this_showroom"></textarea>
                                <div v-if="form.errors.comment" class="text-danger">{{ form.errors.comment }}</div>
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
    name: 'Create',
    components: {
        Button,
        TopBar,
        BackIcon,
        BreezeAuthenticatedLayout,
    },
    data(){
        return {
            lang: this.$page.props.lang,
            form: this.$inertia.form({
                name: '',
                address: '',
                comment: '',
            }),
        }
    },
    methods:{
        saveShowroom(){
            this.form.post(this.route('showroom.store'))
        }
    },

    mounted() {

    }

}
</script>
