<template>
    <div>
        <breeze-authenticated-layout>
            <top-bar :index-route="route('cash.index')" :create-route="route('cash.create')" />
            <div class="container sale">
                <!-- table -->
                <div class="border-0 card">
                    <div class="mb-5 border-0 card">
                        <div class="p-0 border-0 card-header d-flex">
                            <!-- page title -->
                            <div class="mt-3">
                                <h4 class="main-title">{{ lang.create_new_cash }}</h4>
                                <p>
                                    <small
                                        >{{ lang.all }}
                                        <span style="color: red">*</span>
                                         {{lang.mark_must_be_required}}
                                        </small
                                    >
                                </p>
                            </div>

                            <!-- header icon -->
                            <back-icon :Url="route('cash.index')" />
                        </div>
                    </div>

                    <form @submit.prevent="saveCash()" method="POST">
                        <div class="row mb-3">
                            <div class="col-2">
                                <label for="showroom" class="form-label mt-1">{{ lang.showroom }}</label>
                            </div>

                            <div class="col-6">
                                <select name="showroom" class="form-select" v-model="form.showroom_id" id="showroom">
                                    <option :value="null">{{ lang.select_showroom }}</option>
                                    <option v-for="(showroom, index) in showrooms" :key="index" :value="showroom.id">
                                        {{ showroom.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.showroom_id" class="text-danger">{{ form.errors.showroom_id }}</div>
                            </div>
                        </div>
                        <!-- type text -->
                        <div class="mb-3 row">
                            <div class="col-2">
                                <label
                                    for="cash_title"
                                    class="mt-1 form-label required"
                                    >{{ lang.cash_name }}</label
                                >
                            </div>

                            <div class="col-6">
                                <input
                                    type="text"
                                    v-model="form.title"
                                    class="form-control"
                                    id="cash_title"
                                    :placeholder="lang.main_cash"
                                />
                                <div
                                    v-if="form.errors.title"
                                    class="text-danger"
                                >
                                    {{ form.errors.title }}
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-2">
                                <label
                                    for="balance"
                                    class="mt-1 form-label required"
                                    >{{ lang.balance }}</label
                                >
                            </div>

                            <div class="col-6">
                                <input
                                    type="number"
                                    step="any"
                                    v-model="form.balance"
                                    class="form-control"
                                    id="balance"
                                    placeholder="50000.00"
                                />
                                <div
                                    v-if="form.errors.balance"
                                    class="text-danger"
                                >
                                    {{ form.errors.balance }}
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-2">
                                <label class="mt-1 form-label">&nbsp;</label>
                            </div>

                            <div class="col-10">
                                <button
                                    type="reset"
                                    class="btn btn-warning me-2"
                                >
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
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
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
    props: {
        oldCash: Object,
        showrooms: Array,
    },
    data() {
        return {
            lang:this.$page.props.lang,
            form: this.$inertia.form({
                title: "",
                balance: "",
                showroom_id: null,
            }),
        };
    },
    methods: {
        saveCash() {
            if (this.oldCash) {
                this.form.put(this.route("cash.update", this.oldCash.id));
            }else{
                this.form.post(this.route("cash.store"));
            }
        },

        initialValue() {
            if (this.oldCash) {
                this.form.title = this.oldCash.title
                this.form.balance = this.oldCash.balance
                this.form.showroom_id = this.oldCash.showroom_id
            }
        },
    },

    mounted() {
        this.initialValue()
    },
};
</script>
