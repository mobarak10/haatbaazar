<template>
    <!-- aside -->
    <aside class="setting-pages-aside">
        <h5>My Account</h5>
        <ul class="list-group">
            <li>
                <a href="user_profile.html" class="list-group-item active list-group-item-action">Profile Details</a>
            </li>
            <li>
                <a href="attendance.html" class="list-group-item list-group-item-action">Attendance</a>
            </li>
            <li>
                <a href="change_password.html" class="list-group-item list-group-item-action">Change Password</a>
            </li>
        </ul>
    </aside>
    <!-- end aside -->


    <!-- setting-pages-aside-layer -->
    <div class="setting-pages-aside-layer"></div>
    <!-- End setting-pages-aside-layer -->

    <!-- setting pages main ber -->
    <section class="setting-pages-mainber mb-5">


        <!-- header -->
        <header class=" py-2">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <!-- Expand Button -->
                    <div class="expand-button">
                        <input type="checkbox" class="btn-check" id="aside-expand-button" autocomplete="off"
                            onclick="asideExpand()">
                        <label class="btn btn-outline-success p-1" for="aside-expand-button">
                            <i class="bi bi-list-nested"></i>
                        </label>
                    </div>
                    <inertia-link :href="route('dashboard')" class="btn-close ms-auto" aria-label="Close"></inertia-link>
                </div>
            </div>
        </header>
        <!-- End header -->
        <slot/>

    </section>
    <!-- End setting pages main ber -->
</template>

<script>
export default {
}
</script>
