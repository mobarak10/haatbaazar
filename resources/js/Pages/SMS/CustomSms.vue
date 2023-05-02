<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container print-none">
                <ul class="mt-2 nav nav-tabs">
                    <li class="nav-item">
                        <inertia-link
                            class="nav-link active"
                            :href="route('sms.customSms')">
                            {{ lang.custom_sms }}
                        </inertia-link>
                    </li>

                    <li class="nav-item">
                        <inertia-link
                            class="nav-link"
                            :href="route('sms.groupSms')">
                            {{ lang.group_sms }}
                        </inertia-link>
                    </li>
                </ul>
            </div>

            <div class="customer container">
                <!-- table -->
                <div class="card border-0">
                    <div class="mx-3 d-flex justify-content-between align-items-center">
                        <!-- page title -->
                        <div class="mt-3 mb-2 print-none">
                            <h4 class="main-title">{{ lang.custom_sms }}</h4><br>
                        </div>
                    </div>

                    <div class="mt-2">
                        <p class="mb-2 text-muted"> 1. Type Mobile Number and Use Comma to separate more than one Number..</p>
                        <p class="mb-2 text-muted"> 2. Type Message  and then click Send button to Send SMS.</p>
                        <div class="mt-4">
                            <strong>
                                Remaining SMS : {{ remaining_sms }}&nbsp;&nbsp;&nbsp;
                                Sent SMS : {{ send_sms }}
                            </strong>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <!-- Write phone Start-->
                    <div class="col-12">
                        <label for="message" class="mt-1 form-label required">{{ lang.mobile }}</label>
                        <textarea
                            v-model="mobiles"
                            class="form-control"
                            id="mobile"
                            rows="4"
                            placeholder="Type mobile here.."
                            required>
                        </textarea>
                    </div>
                    <!-- Write phone End-->

                    <!-- Write Message Start-->
                    <div class="col-12">
                        <label for="message" class="mt-1 form-label required">{{ lang.sms }}</label>
                        <textarea
                            v-model="message"
                            class="form-control"
                            @click="smsCount"
                            @change="smsCount"
                            @keyup="smsCount"
                            @blur="smsCount"
                            id="message"
                            rows="4"
                            placeholder="Type message here.."
                            required>
                        </textarea>

                    </div>
                    <!-- Write Message End-->

                    <!-- SMS & Character count start -->
                    <div class="col-md-12 mt-3">
                        <p class="text-muted">
                            <span class="row">
                                <span class="col-6">
                                    <strong>Total Characters</strong>
                                    <input class="form-control" type="text" id="total_characters" v-model="total_characters" readonly>
                                </span>
                                <span class="col-6">
                                    <strong>Total Messages</strong>
                                    <input class="form-control" type="text" id="total_messages" v-model="total_messages" readonly>
                                </span>
                            </span>
                        </p>
                    </div>
                    <!-- SMS & Character count end -->
                </div>

                <div class="mb-3 row">
                    <div class="col-2">
                        <label class="mt-1 form-label">&nbsp;</label>
                    </div>

                    <div class="col-12">
                        <button type="reset" class="btn btn-danger me-2">
                            <i class="bi bi-dash-square"></i>
                            <span class="p-1">{{ lang.reset }}</span>
                        </button>

                        <button type="submit" @click="sendCustomSms" :disabled="processing"  class="btn btn-primary" :title="lang.send">
                            <i class="bi bi-envelope"></i>
                            {{ lang.send }}
                            <span
                                v-if="processing"
                                class="spinner-border spinner-border-sm"
                                role="status"
                                aria-hidden="true"
                            ></span>
                        </button>

                    </div>
                </div>
            </div>
        </breeze-authenticated-layout>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import smsCounter from "@/Helpers/smsCounter";
import Pagination from '@/Components/Pagination'
import alert from "@/alert";
export default {
    name: "CustomSms",
    components: {
        BreezeAuthenticatedLayout,
        Pagination,
    },
    props: {
        customers: Object,
        remaining_sms: String,
        send_sms: Number,
    },
    data() {
        return {
            lang: this.$page.props.lang,
            mobiles: null,
            total_characters: 0,
            message: null,
            total_messages: 1,
            processing: false,
        }
    },

    methods: {
        smsCount() {
            if(this.message) {
                let type = smsCounter(this.message)['type'];
                if(type == 'english') {
                    this.total_characters = (this.message.length + 75)
                }else{
                    this.total_characters = (this.message.length + 27)
                }
                this.total_messages = smsCounter(this.message)['total_messages']
            }
        },

        sendCustomSms() {
            this.processing = true
            this.$inertia.visit('/sms/custom-sms', {
                method: 'post',
                data: {
                    mobiles: this.mobiles,
                    total_characters: this.total_characters,
                    message: this.message,
                    total_messages: this.total_messages,
                },
                preserveState: true,
                onSuccess: page => {
                    this.mobiles = null
                    this.total_characters = 0
                    this.message = null
                    this.total_messages = 1
                    this.processing = false
                },
                onError: errors => {
                    this.processing = false
                    if (errors) {
                        alert().fire({
                            icon: 'warning',
                            title: 'Please check phone number'
                        })
                    }
                },
            })
        },
    },
}
</script>

<style scoped>

</style>
