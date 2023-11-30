<template>
  <div class="customer-details">
    <h1>Customer Details</h1>
    <div v-if="isLoading">Loading...</div>
    <div v-else>
      <router-link :to="'/customers'">Back to Customers</router-link>
      <form @submit.prevent="updateCustomer">
        <table>
          <tr v-for="(value, key) in editedCustomer" :key="key">
            <td>{{ key }}</td>
            <span v-if="nonEditableFields.includes(key)">{{ editedCustomer[key] }}</span>
            <input v-else-if="key === 'password'" type="password" v-model="editedCustomer[key]" />
            <input v-else v-model="editedCustomer[key]" />
            <span v-if="validationErrors[key]" class="error-message">{{ validationErrors[key] }}</span>
          </tr>
        </table>
        <button type="submit" class="button">Save</button>
      </form>
    </div>
  </div>
</template>

<script lang="ts">
import { mapState } from 'vuex'
import type { Customer } from '../types/types'
import ValidationService from '../services/ValidationService'

export default {
  name: 'CustomerDetailsView',

  data() {
    return {
      editedCustomer: {} as Customer,
      nonEditableFields: ['id', 'created_at', 'updated_at'],
      validationErrors: {} as Record<string, string>
    }
  },

  methods: {
    validateForm() {
      this.validationErrors = ValidationService.validateCustomerData(this.editedCustomer)

      return Object.keys(this.validationErrors).length === 0
    },

    async loadCustomerDetails(this: {
      $store: { dispatch: (action: string, customerId: string) => Promise<void> }
      $route: { params: { id: string } }
      editedCustomer: { password: string }
      customer: {}
    }) {
      try {
        const customerId = this.$route.params.id
        await this.$store.dispatch('fetchCustomer', customerId)

        this.editedCustomer = { ...this.customer, password: '' }
      } catch (error) {
        console.error('Error fetching customer details:', error)
      }
    },

    async updateCustomer() {
      try {
        if (!this.validateForm()) {
          // Form validation failed
          return
        }

        // Call an action to update the customer data in the store
        await this.$store.dispatch('updateCustomer', this.editedCustomer)
      } catch (error) {
        console.error('Error updating customer:', error)
      }
    }
  },

  created(this: { loadCustomerDetails: () => void }) {
    this.loadCustomerDetails()
  },

  computed: {
    ...mapState({
      customer: (state: { customer: { data: Customer } }) => state.customer.data,
      isLoading: (state: { isLoading: boolean }) => state.isLoading
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
