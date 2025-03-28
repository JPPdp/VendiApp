package com.example.vendiapp.model

data class LoginResponse(
    val success: Boolean,
    val userId: String?,
    val message: String
)
