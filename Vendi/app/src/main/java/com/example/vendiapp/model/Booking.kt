package com.example.vendiapp.model

data class Booking(
    val booking_id: Int,
    val user_id: Int,
    val vendor_id: Int,
    val booking_status: String,
    val scheduled_date: String
)
