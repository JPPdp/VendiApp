package com.example.vendiapp.utils

import android.content.Context
import android.util.Log
import android.widget.Toast

object ApiUtils {

    // Function to simulate adding a user
    fun addUser(
        context: Context,
        fullName: String,
        email: String,
        phoneNumber: String,
        password: String
    ) {
        if (fullName.isNotEmpty() && email.isNotEmpty() && password.isNotEmpty()) {
            Log.d("ApiUtils", "User added successfully: $fullName, $email, $phoneNumber")
            Toast.makeText(context, "User added successfully!", Toast.LENGTH_SHORT).show()
        } else {
            Log.e("ApiUtils", "Failed to add user: Missing fields.")
            Toast.makeText(context, "Failed to add user. Please fill all required fields.", Toast.LENGTH_SHORT).show()
        }
    }

    // Function to simulate getting a list of users
    fun getUsers(): List<Map<String, String>> {
        return listOf(
            mapOf("fullName" to "John Doe", "email" to "john@example.com", "phone" to "1234567890"),
            mapOf("fullName" to "Jane Smith", "email" to "jane@example.com", "phone" to "0987654321"),
            mapOf("fullName" to "Michael Brown", "email" to "michael@example.com", "phone" to "1122334455")
        )
    }

    // Function to simulate user login
    fun loginUser(email: String, password: String): Boolean {
        val dummyUsers = getUsers()
        for (user in dummyUsers) {
            if (user["email"] == email && password == "password123") {
                Log.d("ApiUtils", "Login successful for $email")
                return true
            }
        }
        Log.e("ApiUtils", "Login failed for $email")
        return false
    }

    // Function to delete a user by email
    fun deleteUser(email: String): Boolean {
        val dummyUsers = getUsers().toMutableList()
        val userToRemove = dummyUsers.find { it["email"] == email }
        return if (userToRemove != null) {
            dummyUsers.remove(userToRemove)
            Log.d("ApiUtils", "User deleted: $email")
            true
        } else {
            Log.e("ApiUtils", "User not found: $email")
            false
        }
    }
}
