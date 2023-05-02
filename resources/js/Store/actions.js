import axios from 'axios'
let baseURL = document.head.querySelector('meta[name="base-url"]').content + '/';
export default {
    loadAllCustomers:({ commit }) => {
        return new Promise((resolve, reject) => {
            axios.post(baseURL+'customer/get-all-active-customers')
                .then(response => {
                    commit('mutationLoadAllCustomers', response.data)
                    resolve()
                })
                .catch(error => {
                    console.log(error)
                    reject()
                })
        })
    },

    loadAllMokams:({ commit }) => {
        return new Promise((resolve, reject) => {
            axios.post(baseURL+'mokam/get-all-mokams')
                .then(response => {
                    commit('mutationLoadAllMokams', response.data)
                    resolve()
                })
                .catch(error => {
                    console.log(error)
                    reject()
                })
        })
    },

    loadAllSuppliers:({ commit }) => {
        return new Promise((resolve, reject) => {
            axios.post(baseURL + 'supplier/get-all-active-suppliers')
                .then(response => {
                    commit('mutationLoadAllSuppliers', response.data)
                    resolve()
                })
                .catch(error => {
                    console.log(error)
                    reject()
                })
        })
    },

    loadAllShowrooms:({commit}) => {
        return new Promise((resolve, reject) => {
            axios.post(baseURL+'showroom/get-all-showroom')
                .then(response => {
                    commit('mutationLoadAllShowrooms', response.data)
                    resolve()
                })
                .catch(error => {
                    console.log(error)
                    reject()
                })
        })
    },

    loadAllCashes:({commit}) => {
        axios.post(baseURL+'cash/get-all-cashes')
            .then(response => {
                commit('mutationLoadAllCashes', response.data)
            })
            .catch(error => {
                console.log(error)
            })
    },

    loadAllBankAccounts:({commit}) => {
        axios.post(baseURL+'bank-account/get-all-bank-account')
            .then(response => {
                commit('mutationLoadAllBankAccounts', response.data)
            })
            .catch(error => {
                console.log(error)
            })
    },
}
