package com.example.vendiapp.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.vendiapp.model.Event
import com.example.vendiapp.repository.EventRepository

class EventViewModel : ViewModel() {

    private val eventRepository = EventRepository() // Use Repository for data fetching

    private val _events = MutableLiveData<List<Event>>()
    val events: LiveData<List<Event>> get() = _events

    init {
        loadEvents("Food") // Default to Food events
    }

    fun loadEvents(category: String) {
        _events.value = eventRepository.getEvents(category) // Fetch from repository
    }
}
