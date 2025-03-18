package com.example.vendiapp.view.main.test

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import com.example.vendiapp.R
import com.example.vendiapp.utils.ApiUtils.addUser
import com.example.vendiapp.utils.ApiUtils.getUsers

class TestActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_test)

        // Add a test user
        addUser(
            this, // Pass context here
            "Test User",
            "testuser@example.com",
            "9876543210",
            "password123"
        )

        // Fetch and print all users
        val users = getUsers()
        for (user in users) {
            println("User: ${user["fullName"]}, Email: ${user["email"]}")
        }
    }
}
