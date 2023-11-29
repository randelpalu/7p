<template>
  <div class="customersTable">
    <h1>Welcome to the Customers List</h1>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>First name</th>
          <th>Last name</th>
          <th>dob</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(customer, index) in customersList.data" :key="index">
          <td>{{ customer.id }}</td>
          <td>
            <router-link :to="{ name: 'customer-details', params: { id: customer.id } }">
              {{ customer.username }}
            </router-link>
          </td>
          <td>{{ customer.first_name || '' }}</td>
          <td>{{ customer.last_name || '' }}</td>
          <td>{{ customer.dob || '' }}</td>
          <td>
            <button @click="deleteCustomer(customer.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script lang="ts">
import { mapState } from 'vuex'
import type { Customer } from '../types/types'

export default {
  name: 'CustomersListView',

  methods: {
    async loadCustomers(this: { $store: { dispatch: (action: string) => Promise<void> } }) {
      try {
        await this.$store.dispatch('fetchCustomerList')
      } catch (error) {
        console.error('Error fetching customer list:', error)
      }
    },
    async deleteCustomer(id: number) {
      try {
        const deletionConfirmed = window.confirm('Are you sure you want to delete this customer?');

        if (!deletionConfirmed) {
          return;
        }

        await this.$store.dispatch('deleteCustomer', id)
        this.$store.commit('removeCustomerFromList', id)
      } catch (error) {
        console.error('Error deleting the customer:', error)
      }
    }

  },

  created(this: { loadCustomers: () => void }) {
    this.loadCustomers()
  },

  computed: {
    ...mapState({
      customersList: (state: { customersList: { data: Customer[]; message: string } }) =>
        state.customersList
    })
  }
}
</script>

<style>
@media (min-width: 1024px) {
  .about {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}
</style>
