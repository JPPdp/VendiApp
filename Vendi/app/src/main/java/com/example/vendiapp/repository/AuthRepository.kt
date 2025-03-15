package com.example.vendiapp.repository

import android.os.Handler
import android.os.Looper

class AuthRepository {

    fun login(email: String, password: String, callback: (Boolean) -> Unit) {
        // Simulating network delay (replace this with actual API or Firebase logic)
        Handler(Looper.getMainLooper()).postDelayed({
            if (email == "user@example.com" && password == "password") {
                callback(true) // Login successful
            } else {
                callback(false) // Login failed
            }
        }, 1500) // Simulating a 1.5-second delay
    }
}
