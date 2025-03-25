package com.example.vendiapp.viewmodel

import android.os.Bundle
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.vendiapp.model.EventModel
import com.example.vendiapp.model.PackageModel

class EventDetailsViewModel : ViewModel() {

    private val _event = MutableLiveData<EventModel?>()
    val event: LiveData<EventModel?> = _event

    private val _packages = MutableLiveData<List<PackageModel>>()
    val packages: LiveData<List<PackageModel>> = _packages

    // ✅ Load event data from bundle
    fun loadEventFromBundle(bundle: Bundle) {
        val id = bundle.getInt("eventId", 0)
        val title = bundle.getString("eventTitle", "") ?: ""
        val subTitle = bundle.getString("eventSubTitle", "") ?: ""
        val description = bundle.getString("eventDescription", "") ?: ""
        val location = bundle.getString("eventLocation", "") ?: ""
        val price = bundle.getString("eventPrice", "") ?: ""
        val rating = bundle.getDouble("eventRating", 0.0)
        val imageUrl = bundle.getString("eventImage", "") ?: ""  // ✅ Fixed
        val vendorId = bundle.getInt("vendorId", 0)
        val isFeatured = bundle.getBoolean("isFeatured", false)
        val category = bundle.getString("category", "") ?: ""

        // ✅ Corrected to match EventModel
        val eventModel = EventModel(
            id = id,
            title = title,
            subTitle = subTitle,
            description = description,
            location = location,
            price = price,
            rating = rating,
            imageUrl = imageUrl,
            vendorId = vendorId,
            isFeatured = isFeatured,
            category = category
        )

        _event.value = eventModel
    }

    // ✅ Load packages (mock data or from API)
    fun loadEventPackages() {
        val samplePackages = listOf(
            PackageModel(1, "Basic Package", "Description 1", 5000),
            PackageModel(2, "Premium Package", "Description 2", 10000)
        )
        _packages.value = samplePackages
    }
}
