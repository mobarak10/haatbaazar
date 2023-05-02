<template>
    <div>
        <breeze-authenticated-layout>
            <div class="container print-none">
                <ul class="mt-2 nav nav-tabs">
                    <li class="nav-item">
                        <inertia-link
                            class="nav-link"
                            :href="route('sms.customSms')">
                            {{ lang.custom_sms }}
                        </inertia-link>
                    </li>

                    <li class="nav-item">
                        <inertia-link
                            class="nav-link active"
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
                            <h4 class="main-title">{{ lang.group_sms }}</h4><br>
                        </div>

                        <div class="print-none">
                            <select
                                style="min-width: 65px"
                                @change="getPaginateCustomer"
                                v-model="paginate_number"
                                class="form-select"
                                id="paginate_number"
                            >
                                <option value="30">30</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-2">
                        <p class="mb-2 text-muted"> 1. If don't want send SMS Please Uncheck Customer.</p>
                        <p class="mb-2 text-muted"> 2. Type Message  and then click Send button to Send SMS.</p>
                        <div class="mt-4">
                            <strong>
                                Remaining SMS : {{ remaining_sms }}&nbsp;&nbsp;&nbsp;
                                Sent SMS : {{ send_sms }}
                            </strong>
                        </div>
                    </div>

                    <!-- content body -->
                    <div class="card-body p-0">
                        <div class="table-responsive-md">
                            <table class="table custom-table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" width="5%">
                                            <label for="check-all" class="form-check form-check-inline">
                                                <input
                                                    type="checkbox"
                                                    v-model="allChecked"
                                                    @change="toggleAllChecked"
                                                    class="form-check-input"
                                                    id="check-all"
                                                >
                                                <span>{{ lang.sl }}</span>
                                            </label>
                                        </th>
                                        <th scope="col">
                                            {{ lang.company_name }}
                                        </th>
                                        <th scope="col">{{ lang.customer_name }}</th>
                                        <th scope="col" class="text-end">{{ lang.phone }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(customer,index) in customers.data" :key="index">
                                        <th scope="row">
                                            <label class="form-check form-check-inline">
                                                <input
                                                    v-model="selectedPhoneNumbers"
                                                    type="checkbox"
                                                    @change="isAllChecked"
                                                    :value="customer.phone"
                                                    class="form-check-input"
                                                >
                                                {{ index + 1 }}
                                            </label>
                                        </th>
                                        <td>{{ customer.company_name }}</td>
                                        <td>{{ customer.name }}</td>
                                        <td class="text-end">{{ customer.phone }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- pagination -->
                    <pagination :links="customers.links" />
                </div>
                <div class="mb-3 row">
                    <!-- Write Message Start-->
                    <div class="col-12">
                        <label for="message" class="mt-1 form-label required">Message</label>
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
                            <span>
                                <strong>Total Characters</strong>
                                    <input type="text" id="total_characters" v-model="total_characters" readonly>
                            </span>
                            &nbsp;
                            <span>
                                <strong>Total Messages</strong>
                                    <input  type="text" id="total_messages" v-model="total_messages" readonly>
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

                        <button type="submit" @click="sendGroupSms" :disabled="processing"  class="btn btn-primary" :title="lang.send">
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
    name: "GroupSms",
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
            paginate_number: 30,
            allChecked: false,
            selectedPhoneNumbers: [],
            total_characters: 0,
            message: null,
            total_messages: 1,
            processing: false,
        }
    },

    methods: {
        getPaginateCustomer() {
            this.$inertia.visit('/sms/group-sms', {
                method: 'get',
                data: {
                    paginate_number: this.paginate_number,
                },
                preserveState: true,
            })
        },

        toggleAllChecked() {
            if (this.allChecked) {
                this.customers.data.forEach(customer => {
                    if (!this.selectedPhoneNumbers.includes(customer.phone)) {
                        this.selectedPhoneNumbers.push(customer.phone)
                    }
                })
            }else{
                this.selectedPhoneNumbers = []
            }
        },

        isAllChecked() {
            this.allChecked = this.customers.data.length === this.selectedPhoneNumbers.length
        },

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

        sendGroupSms() {
            this.processing = true
            this.$inertia.visit('/sms/group-sms', {
                method: 'post',
                data: {
                    selectedPhoneNumbers: this.selectedPhoneNumbers,
                    total_characters: this.total_characters,
                    message: this.message,
                    total_messages: this.total_messages,
                },
                preserveState: true,
                onSuccess: page => {
                    this.processing = false
                    this.selectedPhoneNumbers = []
                    this.total_characters = 0
                    this.message = null
                    this.total_messages = 1
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
