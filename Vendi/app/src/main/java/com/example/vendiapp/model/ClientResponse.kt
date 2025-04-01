package com.example.vendiapp.model

data class ClientResponse(
    val success: Boolean,
    val message: String?,
    val data: Any? = null // Optional if you want to handle additional data

)
