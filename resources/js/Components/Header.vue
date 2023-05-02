<template>
    <header>
        <nav class="navbar page-navbar navbar-expand-lg mb-3 navbar-light bg-light">
            <div class="container-fluid">
                <!-- Aside expand button -->
                <div class="expand-button">
                    <button class="btn btn-success p-1" @click.prevent="$emit('sliderClicked')">
                        <i class="bi bi-list-nested"></i>
                    </button>
                </div>
                <!-- End aside expand button -->

                <!-- Responsive button -->
                <button class="navbar-toggler p-1 ms-auto" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <!-- End responsive button -->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav my-2 text-end ms-auto mb-lg-0 py-3 py-lg-0">

                        <li class="nav-item dropdown">
                            <a
                                href="#"
                                class="nav-link"
                                @click.prevent="changeLanguage">
                                <span class="d-lg-none me-3">Language</span>
                                <i class="bi bi-translate"></i>
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link " href="#"  id="settings-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-lg-none me-3">Settings</span>
                                <i class="bi bi-gear"></i>
                            </a>

                            <div class="dropdown-menu settings-dropdown"  aria-labelledby="settings-dropdown">
                                <div class="col-12">
                                   <div class="row">
                                       <div class="col-4">
                                           <ul class="list-group list-group-flush">
                                               <li class="list-group-item pb-2"><strong>{{ lang.settings }} </strong></li>
                                               <li v-if="permissions.includes('showroom-view')" class="list-group-item">
                                                   <inertia-link
                                                       :href="route('showroom.index')"
                                                       class="list-group-item-action">
                                                       <i class="bi bi-building"></i>
                                                       {{ lang.showroom }}
                                                   </inertia-link>
                                               </li>

                                               <li v-if="permissions.includes('unit-view')" class="list-group-item">
                                                   <inertia-link
                                                       :href="route('unit.index')"
                                                       class="list-group-item-action">
                                                       <i class="bi bi-calculator"></i>
                                                       {{ lang.unit }}
                                                   </inertia-link>
                                               </li>

                                               <li v-if="permissions.includes('category-view')" class="list-group-item">
                                                   <inertia-link
                                                       :href="route('category.index')"
                                                       class="list-group-item-action">
                                                       <i class="bi bi-bookmarks"></i>
                                                       {{ lang.category }}
                                                   </inertia-link>
                                               </li>

                                               <li v-if="permissions.includes('brand-view')" class="list-group-item">
                                                   <inertia-link
                                                       :href="route('brand.index')"
                                                       class="list-group-item-action">
                                                       <i class="bi bi-file-earmark-code"></i>
                                                      {{lang.brand}}
                                                   </inertia-link>
                                               </li>
                                           </ul>
                                       </div>
                                       <div class="col-4">
                                           <ul class="list-group list-group-flush">
                                               <li class="list-group-item pb-2"><strong>{{ lang.settings }} </strong></li>
                                               <li v-if="permissions.includes('product-view')" class="list-group-item">
                                                   <inertia-link
                                                       :href="route('product.index')"
                                                       class="list-group-item-action">
                                                       <i class="bi bi-file-earmark-ppt"></i>
                                                      {{lang.product}}
                                                   </inertia-link>
                                               </li>

                                               <li v-if="permissions.includes('cash-view')" class="list-group-item">
                                                   <inertia-link
                                                       :href="route('cash.index')"
                                                       class="list-group-item-action">
                                                       <i class="bi bi-cash"></i>
                                                      {{lang.cash}}
                                                   </inertia-link>
                                               </li>

                                               <li v-if="permissions.includes('bank-view')" class="list-group-item">
                                                   <inertia-link
                                                       :href="route('bank.index')"
                                                       class="list-group-item-action">
                                                       <i class="bi bi-bank"></i>
                                                       {{ lang.bank }}
                                                   </inertia-link>
                                               </li>

                                               <li v-if="permissions.includes('bank_account-view')" class="list-group-item">
                                                   <inertia-link
                                                       :href="route('bank-account.index')"
                                                       class="list-group-item-action">
                                                       <i class="bi bi-person-bounding-box"></i>
                                                       {{ lang.bank_account }}
                                                   </inertia-link>
                                               </li>

                                           </ul>
                                       </div>
                                       <div class="col-4">
                                           <ul class="list-group list-group-flush">
                                               <li class="list-group-item pb-2"><strong>{{ lang.user_management  }}</strong></li>
                                               <li v-if="permissions.includes('user-view')" class="list-group-item">
                                                   <inertia-link
                                                       :href="route('user.index')"
                                                       class="list-group-item-action">
                                                       <i class="bi bi-person"></i>
                                                       {{ lang.user }}
                                                   </inertia-link>
                                               </li>
                                               <li v-if="permissions.includes('role-view')" class="list-group-item">
                                                   <inertia-link
                                                       :href="route('role.index')"
                                                       class="list-group-item-action">
                                                       <i class="bi bi-file-earmark-lock2"></i>
                                                      {{lang.roles}}
                                                   </inertia-link>
                                               </li>
                                           </ul>
                                       </div>
                                   </div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" id="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-lg-none me-3">Profile</span>
                                <i class="bi bi-person"></i>
                            </a>
                            <ul class="dropdown-menu"  aria-labelledby="user-dropdown">
                                <li>
                                    <inertia-link class="dropdown-item" :href="route('user_profile')">
                                        <div class="d-flex align-items-center my-2">
                                            <img src="/public/images/user/user.png" class="user-icon" alt="">
                                            <div class="ms-2">
                                                <h6>{{ $page.props.auth.user.name }}</h6>
                                                <div class="text-small">
                                                    {{ $page.props.auth.user.email }}
                                                </div>
                                            </div>
                                        </div>
                                    </inertia-link>
                                </li>
<!--                                <hr>-->
<!--                                <li>-->
<!--                                    <inertia-link :href="route('user.edit')" method="POST" class="dropdown-item">-->
<!--                                        Update Profile-->
<!--                                    </inertia-link>-->
<!--                                </li>-->
<!--                                <li><a class="dropdown-item" href="change_password.html">Change Password</a></li>-->
<!--                                <hr>-->
                                <li>
                                    <inertia-link :href="route('logout')" method="POST" class="dropdown-item">
                                        Log Out
                                    </inertia-link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</template>

<script>
    export default {
        data() {
            return{
                lang: this.$page.props.lang,
                permissions: this.$page.props.permissionNames,
                form: this.$inertia.form({
                }),
            }
        },
        methods: {
            changeLanguage() {
                this.form.get(this.route("switchLanguage"), {
                    preserveScroll: true,
                });
            },
        },
        mounted() {
        }
    }
</script>
