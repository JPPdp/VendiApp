package com.example.vendiapp.model
data class EventModel(
    val id: Int,
    val title: String,
    val subTitle: String,
    val description: String,
    val location: String,
    val price: String,
    val rating: Double,
    val imageUrl: String,  // ✅ Changed from Int to String
    val vendorId: Int,
    val isFeatured: Boolean,
    val category: String
)
