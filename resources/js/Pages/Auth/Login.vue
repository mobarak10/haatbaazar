<template>
    <!-- authentication -->
    <section class="authentication">
        <!-- main wrapper -->
        <div class="wrapper">
            <!-- alert -->
            <validation-errors/>
            <div class="wrap-content">
                <div class="p-4 pt-2">
                    <!-- Brand logo -->
                    <div class="text-center">
                        <img src="public/images/logos/logo_with_name.svg" class="logo" alt="Brand logo">
                    </div>
                    <hr>

                    <!-- page title -->
                    <div class="text-center mt-2 mb-3">
                        <h3 class="main-title">Sign In</h3>
                    </div>

                    <!-- form -->
                    <form action="" @submit.prevent="submit">
                        <div class="row g-3">
                            <!-- user ID -->
                            <div class="col-12">
                                <label for="user" class="form-label required">User ID</label>
                                <input type="email" class="form-control" id="user" placeholder="access@example.com" v-model="form.email" required>
                            </div>

                            <!-- password input -->
                            <div class="col-12">
                                <label for="password" class="form-label required">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" v-model="form.password" id="password"
                                        placeholder="**********" required>
                                    <a href="#" class="pass-eye" @click="show"><i class="bi bi-eye-fill"></i></a>
                                </div>
                            </div>

                            <!-- checkbox input -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input" v-model="form.remember" id="remember" value="">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                            </div>

                            <!-- button -->
                            <div class="col-12">
                                <button type="submit" class="btn w-100 btn-success custom-btn mr-2" :disabled="form.processing">
                                    <i class="bi bi-lock"></i>
                                    <span>Sign in</span>
                                    <span
                                        v-if="form.processing"
                                        class="spinner-border spinner-border-sm"
                                        role="status"
                                        aria-hidden="true"
                                    ></span>
                                </button>
                            </div>
                        </div>
                    </form>
                    <hr>
<!--                    <div class="mt-2 text-center">-->
<!--                        &lt;!&ndash; forgot password &ndash;&gt;-->
<!--                        <small class="d-block"><a href="forgot_password.html" >I forgot my user ID or Password</a></small>-->

<!--                        &lt;!&ndash; Sign Up &ndash;&gt;-->
<!--                        &lt;!&ndash; <small class="d-block">New to the system? <a href="signup.html">Sign up</a></small> &ndash;&gt;-->
<!--                    </div>-->
                </div>
            </div>

            <!-- Copyright -->
            <div class="row text-light text-small mt-2">
                <div class="col-md-8">
                    &copy; 2020
                    <a href="https://haatbaazar.com/" class="text-small" target="_blank">Haat Baazar</a>.
                    <span> All rights reserved.</span>
                </div>

                <div class="col-md-4 text-end" style="word-spacing: 5px;">
                    <a href="#" class="text-small" target="_blank">Privacy</a> |
                    <a href="#" class="text-small" target="_blank">Support</a>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import ValidationErrors from '@/Components/Login/ValidationErrors'

    export default {
        components: {
            ValidationErrors
        },
        props: {
            auth: Object,
            env_status: String,
            canResetPassword: Boolean,
            errors: Object,
            status: String,
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: '',
                    password: '',
                    remember: false,
                })
            }
        },

        methods: {
            show() {
                let input = document.getElementsByName("password")[0],
                    type = input.getAttribute("type");

                if (type === "password") {
                    input.type = "text";
                    document.querySelector('.bi-eye-fill').classList.add("bi-eye-slash-fill");
                } else {
                    input.type = "password";
                    document.querySelector('.bi-eye-fill').classList.remove("bi-eye-slash-fill");
                }
            },
            submit() {
                this.form.post(this.route('login'), {
                    onFinish: () => this.form.reset('password'),
                })
            }
        },

        mounted() {
            if (this.env_status === 'local'){
                this.form.email = "admin@haatbaazar.com"
                this.form.password = "password"
            }
        }
    }
</script>
