package com.example.vendiapp.viewmodel

import android.os.Bundle
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.vendiapp.model.VendorModel
import com.example.vendiapp.model.PackageModel

class EventDetailsViewModel : ViewModel() {

    private val _vendor = MutableLiveData<VendorModel?>()
    val vendor: LiveData<VendorModel?> = _vendor

    private val _packages = MutableLiveData<List<PackageModel>>()
    val packages: LiveData<List<PackageModel>> = _packages

    // Load vendor data from bundle
    fun loadVendorFromBundle(bundle: Bundle) {
        val vendorId = bundle.getInt("vendorId", 0)
        val businessName = bundle.getString("businessName", "") ?: ""
        val profilePicture = bundle.getString("profilePicture") // Can be null
        val email = bundle.getString("email", "") ?: ""
        val mobileNumber = bundle.getString("mobileNumber", "") ?: ""
        val address = bundle.getString("address", "") ?: ""
        val shortDesc = bundle.getString("businessShortDesc") // Can be null
        val longDesc = bundle.getString("businessLongDesc") // Can be null
        val lowestPrice = bundle.getString("lowestPrice") // Can be null
        val rating = bundle.getDouble("rating", 0.0)
        val isFeatured = bundle.getBoolean("isFeatured", false)
        val categoryId = bundle.getInt("categoryId", 0) // Ensuring it's an Int

        // Construct VendorModel with correct attributes
        val vendorModel = VendorModel(
            vendor_id = vendorId,
            business_name = businessName,
            profile_picture = profilePicture,
            email = email,
            mobile_number = mobileNumber,
            address = address,
            business_description_short = shortDesc,
            business_description_long = longDesc,
            lowest_price = lowestPrice,
            rating = rating,
            is_featured = isFeatured,
            category_id = categoryId
        )

        _vendor.value = vendorModel
    }

    // Load sample packages (Mock data or API call)
    fun loadVendorPackages() {
        val samplePackages = listOf(
            PackageModel(1, "Basic Package", "Includes basic services", 5000),
            PackageModel(2, "Premium Package", "Includes premium services", 10000)
        )
        _packages.value = samplePackages
    }
}
