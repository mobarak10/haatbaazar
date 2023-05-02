import getters from "@/Store/getters";
export default {
    mutationLoadAllCustomers: (state, payload) => {
        state.customers = payload
        state.mokamCustomers = payload
    },

    mutationLoadAllSuppliers: (state, payload) => {
        state.suppliers = payload
    },

    mutationLoadAllMokams: (state, payload) => {
        state.mokams = payload
    },

    mutationLoadMokamCustomers: (state, mokamId) => {
        if (mokamId) {
            state.mokamCustomers = state.customers.filter(customer => customer.mokam_id == mokamId)
        }else{
            state.mokamCustomers = state.customers
        }
    },

    mutationLoadAllShowrooms: (state, payload) => {
        state.showrooms = payload
    },

    mutationLoadSubtotal: (state, payload) => {
        state.subtotal = payload
    },

    mutationLoadCustomerBalance: (state, payload) => {
        state.customerBalance = payload
    },

    mutationLoadAllProducts: (state, payload) => {
        state.allProducts = payload
    },

    mutationLoadSupplierBalance: (state, payload) => {
        state.supplierBalance = payload
    },

    mutationLoadOldData: (state, payload) => {
        state.oldData = payload
    },

    mutationLoadAllCashes: (state, payload) => {
        state.cashes = payload
    },

    mutationLoadAllBankAccounts: (state, payload) => {
        state.bankAccounts = payload
    },

    mutationAddPermissions: (state, payload) => {
        if (!state.rolePermissions.includes(payload)) {
            state.rolePermissions.push(payload)
        }
    },

    mutationRemovePermissions: (state, payload) => {
        if (state.rolePermissions.includes(payload)) {
            let index = state.rolePermissions.findIndex(permission => permission == payload)
            state.rolePermissions.splice(index, 1)
        }
    },
}
