package com.example.vendiapp.model

data class EventModel(
    val title: String,
    val subTitle: String,
    val description:String,
    val location: String,
    val price: String,
    val rating: Double,
    val imageRes: Int,
    val isFeatured: Boolean,
    val category: String
)
