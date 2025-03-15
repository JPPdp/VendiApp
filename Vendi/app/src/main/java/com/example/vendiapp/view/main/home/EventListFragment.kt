package com.example.vendiapp.view.main.home

import android.content.Intent
import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.viewModels
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.vendiapp.R
import com.example.vendiapp.adapter.EventAdapter
import com.example.vendiapp.model.EventModel
import com.example.vendiapp.viewmodel.EventViewModel

class EventListFragment : Fragment() {

    private lateinit var eventAdapter: EventAdapter
    private lateinit var recyclerView: RecyclerView
    private val eventViewModel: EventViewModel by viewModels()

    private var lastLoadedCategory: String? = null // Track last loaded category to avoid redundant calls

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val view = inflater.inflate(R.layout.fragment_event_list, container, false)
        recyclerView = view.findViewById(R.id.rvEvents)

        // Retrieve category argument safely (default: "all")
        val category = arguments?.getString("category") ?: "all"

        // Setup RecyclerView
        recyclerView.layoutManager = LinearLayoutManager(requireContext())
        eventAdapter = EventAdapter(emptyList()) { event ->
            navigateToDetails(event)
        }
        recyclerView.adapter = eventAdapter

        // Observe only non-featured events from ViewModel
        eventViewModel.nonFeaturedEvents.observe(viewLifecycleOwner) { events ->
            val filteredEvents = if (category == "all") {
                events
            } else {
                events.filter { it.category.equals(category, ignoreCase = true) }
            }
            eventAdapter.updateEvents(filteredEvents)
        }

        // Load events only if the category has changed
        if (lastLoadedCategory != category) {
            eventViewModel.loadEventsIfNeeded(category)
            lastLoadedCategory = category
        }

        return view
    }

    // Navigate to StallDetailsActivity with event data
    private fun navigateToDetails(event: EventModel) {
        val intent = Intent(requireContext(), EventDetailsActivity::class.java).apply {
            putExtra("eventTitle", event.title)
            putExtra("eventSubTitle", event.subTitle)
            putExtra("eventDescription", event.description)
            putExtra("eventLocation", event.location)
            putExtra("eventPrice", event.price)
            putExtra("eventRating", event.rating)
            putExtra("eventImage", event.imageRes)
        }
        startActivity(intent)
    }

    // Factory method to create fragment instance with category
    companion object {
        fun newInstance(category: String): EventListFragment {
            return EventListFragment().apply {
                arguments = Bundle().apply {
                    putString("category", category)
                }
            }
        }
    }
}