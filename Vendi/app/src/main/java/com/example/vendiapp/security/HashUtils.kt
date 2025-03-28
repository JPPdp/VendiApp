package com.example.vendiapp.security

import android.util.Log
import java.security.MessageDigest

object HashUtils {
    fun hashPassword(password: String): String {
        val bytes = password.toByteArray()
        val md = MessageDigest.getInstance("SHA-256") // Use correct algorithm
        val digest = md.digest(bytes)

        val hashedPassword = digest.joinToString("") { "%02x".format(it) }

        // ✅ Log the hashed password
        Log.d("HASH_UTILS", "Generated Hash: $hashedPassword")
        return hashedPassword
    }
}