package com.example.vendiapp

data class Event(
    val title: String,
    val subTitle: String,
    val description:String,
    val location: String,
    val price: String,
    val rating: Double,
    val imageRes: Int,
    val isFeatured: Boolean
)
