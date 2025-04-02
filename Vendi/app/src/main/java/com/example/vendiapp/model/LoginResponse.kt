package com.example.vendiapp.model

data class LoginResponse(
    val status: String,
    val client_id: String?,
    val name: String?,          // Add if missing
    val mobile_number: String?, // Add if missing
    val message: String?
)
