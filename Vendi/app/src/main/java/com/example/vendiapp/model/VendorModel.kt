package com.example.vendiapp.model

data class VendorModel(
    val vendor_id: Int, // ID of the vendor
    val business_name: String, // Business name
    val profile_picture: String?, // URL for the vendor's profile picture
    val email: String, // Vendor's email
    val mobile_number: String, // Vendor's mobile number
    val address: String, // Address of the vendor
    val business_description_short: String?, // Short description of the business
    val business_description_long: String?, // Long description of the business
    val lowest_price: String?, // Lowest price offered by the vendor (could be a String with currency symbol)
    val rating: Double, // Vendor rating (could be a double)
    val is_featured: Boolean, // Whether the vendor is featured (1 or 0)
    val category_id: Int // The category ID to which the vendor belongs
)
