package com.example.vendiapp.model

data class Event(
    val name: String,
    val location: String,
    val price: String,
    val rating: Double,
    val imageRes: Int,
    val isFeatured: Boolean
)
