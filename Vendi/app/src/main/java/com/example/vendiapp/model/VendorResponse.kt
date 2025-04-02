package com.example.vendiapp.model

data class VendorResponse(
    val status: String,
    val message: String? = null,
    val vendors: List<VendorModel>? = null
)

