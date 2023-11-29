<template>
  <div class="add-customer">
    <h1>Add customer</h1>
    <router-link :to="'/customers'">Back to Customers</router-link>
    <form @submit.prevent="addUser">
      <table>
        <tr>
          <td><label for="username">Username:</label></td>
          <td><input type="text" id="username" v-model="customerData.username" /></td>
          <td v-if="validationErrors.username">{{ validationErrors.username }}</td>
        </tr>
        <tr>
          <td><label for="first_name">First name:</label></td>
          <td><input type="text" id="first_name" v-model="customerData.first_name" /></td>
          <td v-if="validationErrors.first_name">{{ validationErrors.first_name }}</td>
        </tr>
        <tr>
          <td><label for="last_name">Last name:</label></td>
          <td><input type="text" id="last_name" v-model="customerData.last_name" /></td>
          <td v-if="validationErrors.last_name">{{ validationErrors.last_name }}</td>
        </tr>
        <tr>
          <td><label for="dob">Date of birth:</label></td>
          <td><input type="date" id="dob" v-model="customerData.dob" /></td>
          <td v-if="validationErrors.dob">{{ validationErrors.dob }}</td>
        </tr>
        <tr>
          <td><label for="password">Password:</label></td>
          <td><input type="password" id="password" v-model="customerData.password" /></td>
          <td v-if="validationErrors.password">{{ validationErrors.password }}</td>
        </tr>
      </table>
      <button type="submit" class="button">Register</button>
    </form>
  </div>
</template>

<script lang="ts">
import ValidationService from '../services/ValidationService'
import type { Customer } from '../types/types'

export default {
  name: 'AddCustomerView',

  data() {
    return {
      customerData: {
        first_name: '',
        last_name: '',
        username: '',
        dob: '',
        password: ''
      } as Omit<Customer, 'id' | 'created_at' | 'updated_at'>,
      validationErrors: {} as Record<string, string>
    }
  },

  methods: {
    validateForm() {
      this.validationErrors = ValidationService.validateCustomerData(this.customerData)

      return Object.keys(this.validationErrors).length === 0
    },

    async addUser(this: {
        $store: { dispatch: (action: string, customer: Customer) => Promise<number>}
        customerData: Customer
        validateForm: () => boolean
        $router: { push: (location: { name: string, params?: { [key: string]: number } }) => void }
    }) {
      try {
        if (!this.validateForm()) {
          // Form validation failed
          return
        }

        const id = await this.$store.dispatch('addCustomer', this.customerData)

        // Redirect to details page
        this.$router.push({ name: 'customer-details', params: { id } })
        // this.$router.push({ name: 'customer-details', params: { id: id } })
      } catch (error) {
        console.error('Error adding customer:', error)
      }
    }
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
