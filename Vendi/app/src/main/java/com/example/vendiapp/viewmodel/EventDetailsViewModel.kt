package com.example.vendiapp.viewmodel

import android.os.Bundle
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.vendiapp.PackageModel
import com.example.vendiapp.R
import com.example.vendiapp.model.EventModel

class EventDetailsViewModel : ViewModel() {

    private val _event = MutableLiveData<EventModel>()
    val event: LiveData<EventModel> get() = _event

    private val _packages = MutableLiveData<List<PackageModel>>()
    val packages: LiveData<List<PackageModel>> get() = _packages

    // Load event data from Bundle (for Fragments)
    fun loadEventFromBundle(bundle: Bundle) {
        val event = EventModel(
            title = bundle.getString("eventTitle", "Unknown Event"),
            subTitle = bundle.getString("eventSubTitle", "No Subtitle"),
            description = bundle.getString("eventDescription", "No Description Available"),
            location = bundle.getString("eventLocation", "No Location Provided"),
            price = bundle.getString("eventPrice", "No Price Info"),
            rating = bundle.getDouble("eventRating", 0.0),
            imageRes = bundle.getInt("eventImage", R.drawable.img_ezbandph),
            isFeatured = bundle.getBoolean("isFeatured", false),
            category = bundle.getString("category", "Uncategorized")
        )

        _event.value = event
        loadPackages(event.title) // Load related packages dynamically
    }

    // Load packages dynamically based on the selected event
    private fun loadPackages(eventTitle: String) {
        val eventPackages = when (eventTitle) {
            "Lemonology" -> listOf(
                PackageModel("Basic Package", "Good for 25 pax", "₱599", R.drawable.img_lemonology),
                PackageModel("Standard Package", "Good for 50 pax", "₱799", R.drawable.img_lemonology_package2),
                PackageModel("Premium Package", "Good for 100 pax", "₱999", R.drawable.img_lemonology_package3)
            )
            "Potato Corner" -> listOf(
                PackageModel("Solo Fries", "Good for 1 pax", "₱99", R.drawable.img_potatocorner),
                PackageModel("Barkada Fries", "Good for 5 pax", "₱499", R.drawable.img_potatocorner),
                PackageModel("Party Bucket", "Good for 10 pax", "₱899", R.drawable.img_potatocorner)
            )
            else -> emptyList() // No packages available
        }

        _packages.value = eventPackages
    }
}