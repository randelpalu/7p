import type { Customer } from "@/types/types"

export default class ValidationService {
  static validateCustomerData(customerData: Omit<Customer, 'id' | 'created_at' | 'updated_at'>): { [key: string]: string } {
    const validationErrors: { [key: string]: string } = {}

    if (!customerData.first_name || !/^[a-zA-Z]{2,20}$/.test(customerData.first_name)) {
      validationErrors.first_name =
        'First name is required and should consist of 2-20 alphabetical characters'
    }

    if (!customerData.last_name || !/^[a-zA-Z]{2,20}$/.test(customerData.last_name)) {
      validationErrors.last_name =
        'Last name is required and should consist of 2-20 alphabetical characters'
    }
    if (!customerData.username || !/^[a-zA-Z]{4,20}$/.test(customerData.username)) {
      validationErrors.username =
        'Username is required and should consist of 4-20 alphabetical characters'
    }
    if (!customerData.dob) {
      validationErrors.dob = 'Date of birth is required'
    } else {
      // Validate age
      const dobDate = new Date(customerData.dob)
      const currentDate = new Date()
      const minDate = new Date(
        currentDate.getFullYear() - 100,
        currentDate.getMonth(),
        currentDate.getDate()
      )
      const maxDate = new Date(
        currentDate.getFullYear() - 18,
        currentDate.getMonth(),
        currentDate.getDate()
      )

      if (dobDate < minDate || dobDate > maxDate) {
        validationErrors.dob = 'Customers age should be between 18 and 100 years old'
      }
    }
    if (!customerData.password) {
      validationErrors.password = 'Password is required'
    } else {
      // Validate password
      const passwordRegex = /^(?=.*[A-Z])(?=.*\d).{8,}$/
      if (!passwordRegex.test(customerData.password)) {
        validationErrors.password =
          'Password should be at least 8 characters long and include at least one uppercase letter and one digit'
      }
    }

    return validationErrors
  }
}
