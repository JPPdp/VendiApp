package com.example.vendiapp.model

data class Vendor(
    val vendor_id: Int,
    val full_name: String,
    val service_type: String,
    val location: String,
    val availability_status: String,
    val rating: Double
)
