package com.example.vendiapp.model

data class Product(
    val product_id: Int,
    val vendor_id: Int,
    val category_id: Int,
    val product_name: String,
    val price: Double,
    val description: String
)
