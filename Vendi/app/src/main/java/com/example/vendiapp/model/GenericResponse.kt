package com.example.vendiapp.model

// ✅ Generic Response Model for success messages
data class GenericResponse(
    val status: String,
    val message: String,
    val userId: String? = null
)