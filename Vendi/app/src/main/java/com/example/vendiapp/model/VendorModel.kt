package com.example.vendiapp.model
data class VendorModel(

    //
    val vendor_id: Int,
    val business_name: String,
    val short_desc: String,
    val long_desc: String,
    val address: String,
    val price: String,
    val rating: Double,
    //val imageUrl: String,  // ✅ Changed from Int to String
    val isFeatured: Boolean,
    val category_id: String
)
