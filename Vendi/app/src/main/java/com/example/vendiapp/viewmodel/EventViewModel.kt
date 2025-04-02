package com.example.vendiapp.viewmodel

import android.util.Log
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.vendiapp.model.VendorModel
import com.example.vendiapp.repository.EventRepository
import kotlinx.coroutines.launch

class EventViewModel(private val repository: EventRepository = EventRepository()) : ViewModel() {

    // LiveData for different event types
    private val _events = MutableLiveData<List<VendorModel>>()
    val events: LiveData<List<VendorModel>> get() = _events

    private val _nonFeaturedEvents = MutableLiveData<List<VendorModel>>()
    val nonFeaturedEvents: LiveData<List<VendorModel>> get() = _nonFeaturedEvents

    private val _featuredEvents = MutableLiveData<List<VendorModel>>()
    val featuredEvents: LiveData<List<VendorModel>> get() = _featuredEvents

    // State tracking
    private val _loadingState = MutableLiveData<Boolean>()
    val loadingState: LiveData<Boolean> get() = _loadingState

    private val _errorState = MutableLiveData<String?>()
    val errorState: LiveData<String?> get() = _errorState

    private var lastFetchedCategory: String? = null

    /**
     * Loads events for the specified category if needed
     * @param category The category to load events for
     */
    fun loadEventsIfNeeded(category: String) {
        if (_loadingState.value == true) {
            Log.d("EventViewModel", "Already loading events, skipping request for category: $category")
            return
        }

        if (category == lastFetchedCategory && !_events.value.isNullOrEmpty()) {
            Log.d("EventViewModel", "Using cached data for category: $category")
            return
        }

        lastFetchedCategory = category
        _loadingState.value = true
        _errorState.value = null

        viewModelScope.launch {
            try {
                Log.d("EventViewModel", "Fetching events for category: $category from API...")

                val allEvents = repository.getVendorsFromApi(category)

                if (allEvents.isEmpty()) {
                    Log.w("EventViewModel", "No events found for category: $category")
                } else {
                    Log.d("EventViewModel", "Fetched ${allEvents.size} events for category: $category")
                }

                // Update LiveData (force UI refresh)
                _events.postValue(allEvents)
                _nonFeaturedEvents.postValue(allEvents.filter { !it.is_featured })
                _featuredEvents.postValue(allEvents.filter { it.is_featured })

            } catch (e: Exception) {
                val errorMsg = "Error loading events: ${e.localizedMessage}"
                Log.e("EventViewModel", errorMsg, e)
                _errorState.postValue(errorMsg)

                // Ensure UI clears out stale data on failure
                _events.postValue(emptyList())
                _nonFeaturedEvents.postValue(emptyList())
                _featuredEvents.postValue(emptyList())

            } finally {
                _loadingState.postValue(false)
            }
        }
    }

    /**
     * Force refresh events for the current category
     */
    fun refreshEvents() {
        Log.d("EventViewModel", "Forcing refresh for category: $lastFetchedCategory")
        lastFetchedCategory?.let { loadEventsIfNeeded(it) }
    }
}
