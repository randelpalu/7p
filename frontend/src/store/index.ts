import type { Customer } from '@/types/types'
import { createStore } from 'vuex'
import type { Commit, Store } from 'vuex/types/index.js'

type State = {
  customersList: {
    data: Customer[]
    message: string
  }
  customer: Customer | null
  isLoading: boolean
}

type Mutations = {
  setCustomersList(state: State, customers: State['customersList']): void
  setCustomer(state: State, customer: Customer): void
  setLoading(state: State, loading: boolean): void
  removeCustomerFromList(state: State, id: number): void
}

type Actions = {
  fetchCustomerList({ commit }: { commit: Commit }): Promise<void>
  fetchCustomer({ commit }: { commit: Commit }, id: number): Promise<void>
  addCustomer({ commit }: { commit: Commit }, customer: Customer): Promise<void>
  updateCustomer({ commit }: { commit: Commit }, customer: Customer): Promise<void>
  deleteCustomer({ commit }: { commit: Commit }, id: number): Promise<void>
}

function getAuthorizationHeaders() {
  const username = import.meta.env.VITE_API_USERNAME
  const password = import.meta.env.VITE_API_PASSWORD

  const headers = new Headers({
    Authorization: 'Basic ' + btoa(username + ':' + password),
    'Content-Type': 'application/json'
  })

  return headers
}

const store: Store<State> = createStore({
  state: {
    customersList: {
      data: [],
      message: ''
    },
    customer: null,
    isLoading: false
  },

  mutations: {
    setCustomersList(state, customers) {
      state.customersList = customers
    },
    setCustomer(state, customer) {
      state.customer = customer
    },
    setLoading(state, loading) {
      state.isLoading = loading
    },
    removeCustomerFromList(state, deletedCustomerId) {
      state.customersList.data = state.customersList.data.filter(
        customer => customer.id !== deletedCustomerId
      );
    },
  } as Mutations,

  actions: {
    async fetchCustomerList({ commit }) {
      try {
        commit('setLoading', true)

        const apiUrl = `${import.meta.env.VITE_API_URL}/customers`
        const response = await fetch(apiUrl, {
          method: 'GET',
          headers: getAuthorizationHeaders()
        })

        if (!response.ok) {
          throw new Error('Failed to fetch customers list')
        }

        const customers = await response.json()

        commit('setCustomersList', customers)
      } catch (error) {
        console.error('Error fetching customers list:', error)
      } finally {
        commit('setLoading', false)
      }
    },

    async fetchCustomer({ commit }, id) {
      try {
        commit('setLoading', true)

        const apiUrl = `${import.meta.env.VITE_API_URL}/customers/${id}`
        const response = await fetch(apiUrl, {
          method: 'GET',
          headers: getAuthorizationHeaders()
        })

        if (!response.ok) {
          throw new Error('Failed to fetch the customer')
        }

        const customer = await response.json()

        commit('setCustomer', customer)
      } catch (error) {
        console.error('Error fetching the customer:', error)
      } finally {
        commit('setLoading', false)
      }
    },

    async addCustomer({ commit }, customer) {
      try {
        commit('setLoading', true)

        const api = `${import.meta.env.VITE_API_URL}/customers`
        const response = await fetch(api, {
          method: 'POST',
          headers: getAuthorizationHeaders(),
          body: JSON.stringify(customer)
        })

        if (!response.ok) {
          throw new Error('Failed to add the customer')
        }

        const { data } = await response.json()

        return data.id
      } catch (error) {
        console.error('Error adding the customer:', error)
      } finally {
        commit('setLoading', false)
      }
    },

    async updateCustomer({ commit }, customer: Customer) {
      try {
        commit('setLoading', true)

        const api = `${import.meta.env.VITE_API_URL}/customers/${customer.id}`
        const response = await fetch(api, {
          method: 'PUT',
          headers: getAuthorizationHeaders(),
          body: JSON.stringify(customer)
        })

        if (!response.ok) {
          throw new Error('Failed to update the customer')
        }

        const { data } = await response.json()

        return data.id
      } catch (error) {
        console.error('Error updating the customer:', error)
      } finally {
        commit('setLoading', false)
      }
    },

    async deleteCustomer({ commit }, id) {
      try {
        commit('setLoading', true)

        const api = `${import.meta.env.VITE_API_URL}/customers/${id}`
        const response = await fetch(api, {
          method: 'DELETE',
          headers: getAuthorizationHeaders(),
        })

        if (!response.ok) {
          throw new Error('Failed to delete the customer')
        }

        const { message } = await response.json()

        return message
      } catch (error) {
        console.error('Error deleting the customer:', error)
      } finally {
        commit('setLoading', false)
      }
    }
  } as Actions
})

export default store
