package com.example.vendiapp.utils
import java.security.MessageDigest

object HashUtils {
    fun hashPassword(password: String): String {
        val bytes = password.toByteArray()
        val md = MessageDigest.getInstance("SHA-256") // or MD5
        val digest = md.digest(bytes)
        return digest.joinToString("") { "%02x".format(it) }
    }
}