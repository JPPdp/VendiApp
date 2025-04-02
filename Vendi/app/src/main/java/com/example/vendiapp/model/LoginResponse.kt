package com.example.vendiapp.model

data class LoginResponse(
    val success: Boolean,
    val clientId: String,
    val client_name: String,
    val message: String?,
)
