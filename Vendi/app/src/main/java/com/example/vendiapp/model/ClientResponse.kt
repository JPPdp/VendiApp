package com.example.vendiapp.model

// ClientResponse.kt
data class ClientResponse(
    val success: Boolean,
    val message: String? = null,
    // Add any additional fields your API returns
)
