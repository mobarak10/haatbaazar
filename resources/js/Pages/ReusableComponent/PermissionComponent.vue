<template>
    <div class="col-4 mt-2">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <span>{{ menu.name }}</span>
                <span>
                    <input type="checkbox" v-model="allChecked" @change="toggleAllChecked" class="form-check-input">
                </span>
            </div>

            <div class="card-body">
                <div v-for="(permission, permissionIndex) in permissions" :key="permissionIndex">
                    <div class="form-check">
                        <input type="checkbox" v-model="selectedPermissions"
                               class="form-check-input"
                               :value="permission.id"
                               @change="isAllChecked(permission.id)"
                               :id="'permission-' + permission.id">

                        <label class="form-check-label"
                               aria-describedby="permission-help"
                               :for="'permission-' + permission.id">
                            {{ permission.name.split('-')[1].substring(0,1).toUpperCase() + permission.name.split('-')[1].substring(1).replace(/_/g, " ") }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapMutations, mapState} from "vuex";

export default {
    name: "PermissionComponent",
    props: {
        menus: Object,
        permissions: Object,
        menu: Object,
    },

    computed: {
        ...mapState(["rolePermissions"]),
    },
    data() {
        return {
            allChecked: false,
            selectedPermissions: [],
        }
    },

    methods: {
        ...mapMutations(['mutationAddPermissions', 'mutationRemovePermissions']),
        toggleAllChecked() {
            if (this.allChecked) {
                this.permissions.forEach(permission => {
                    if (!this.selectedPermissions.includes(permission.id)) {
                        this.mutationAddPermissions(permission.id)
                        this.selectedPermissions.push(permission.id)
                    }
                })
            }else{
                this.permissions.forEach(permission => {
                    this.mutationRemovePermissions(permission.id)
                })
                this.selectedPermissions = []
            }
        },

        isAllChecked(id) {
            if (this.selectedPermissions.includes(id)){
                this.mutationAddPermissions(id)
            }else{
                this.mutationRemovePermissions(id)
            }
            this.allChecked = this.permissions.length === this.selectedPermissions.length
        },
    },

    mounted() {
        if (this.rolePermissions) {
            this.selectedPermissions = this.rolePermissions
        }
    }
}
</script>

<style scoped>

</style>
