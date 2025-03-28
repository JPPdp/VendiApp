package com.example.vendiapp.viewmodel

import android.util.Log
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.vendiapp.repository.EventRepository
import com.example.vendiapp.model.EventModel

class EventViewModel : ViewModel() {

    private val eventRepository = EventRepository()

    private val _events = MutableLiveData<List<EventModel>>()
    private val events: LiveData<List<EventModel>> get() = _events

    private val _nonFeaturedEvents = MutableLiveData<List<EventModel>>()
    val nonFeaturedEvents: LiveData<List<EventModel>> get() = _nonFeaturedEvents

    private val _featuredEvents = MutableLiveData<List<EventModel>>()
    val featuredEvents: LiveData<List<EventModel>> get() = _featuredEvents

    private var lastFetchedCategory: String? = null
    private var isLoading = false // Prevent multiple simultaneous loads

    fun loadEventsIfNeeded(category: String) {
        if (category == lastFetchedCategory && !events.value.isNullOrEmpty()) {
            Log.d("EventViewModel", "Skipping redundant fetch for category: $category")
            return
        }

        if (isLoading) {
            Log.d("EventViewModel", "Already fetching events, skipping duplicate request.")
            return
        }

        isLoading = true
        lastFetchedCategory = category

        Log.d("EventViewModel", "Fetching events for category: $category")
        val allEvents = eventRepository.getEvents(category)

        _events.postValue(allEvents)
        _nonFeaturedEvents.postValue(allEvents.filter { !it.isFeatured })
        _featuredEvents.postValue(allEvents.filter { it.isFeatured })

        isLoading = false // Reset loading flag after fetch
    }
}

