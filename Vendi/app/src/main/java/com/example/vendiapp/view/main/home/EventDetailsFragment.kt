package com.example.vendiapp.view.main.home

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.fragment.app.Fragment
import androidx.fragment.app.activityViewModels
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.vendiapp.PackageAdapter
import com.example.vendiapp.R
import com.example.vendiapp.model.EventModel
import com.example.vendiapp.viewmodel.EventDetailsViewModel

class EventDetailsFragment : Fragment() {
    private val viewModel: EventDetailsViewModel by activityViewModels()
    private lateinit var packageAdapter: PackageAdapter

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        val view = inflater.inflate(R.layout.fragment_event_details, container, false)

        val eventImage: ImageView = view.findViewById(R.id.ivEventImage)
        val eventName: TextView = view.findViewById(R.id.tvEventName)
        val tvSubEventName: TextView = view.findViewById(R.id.tvSubEventName)
        val tvDescription: TextView = view.findViewById(R.id.tvDescription)
        val eventLocation: TextView = view.findViewById(R.id.tvEventLocation)
        val eventPrice: TextView = view.findViewById(R.id.tvEventPrice)
        val eventRating: TextView = view.findViewById(R.id.tvEventRating)
        val rvEventPackageDetails: RecyclerView = view.findViewById(R.id.rvEventPackageDetails)

        rvEventPackageDetails.layoutManager = LinearLayoutManager(requireContext())
        packageAdapter = PackageAdapter(emptyList())
        rvEventPackageDetails.adapter = packageAdapter

        // Load event details from arguments if available
        arguments?.let { bundle ->
            viewModel.loadEventFromBundle(bundle)
        }

        // Observe ViewModel for event data
        viewModel.event.observe(viewLifecycleOwner) { event ->
            event?.let {
                eventName.text = it.title
                tvSubEventName.text = it.subTitle
                tvDescription.text = it.description
                eventLocation.text = it.location
                eventPrice.text = it.price
                eventRating.text = it.rating.toString()
                eventImage.setImageResource(it.imageRes)
            }
        }

        // Observe ViewModel for package data
        viewModel.packages.observe(viewLifecycleOwner) { packages ->
            packageAdapter.updatePackages(packages)
        }

        return view
    }

    companion object {
        fun newInstance(event: EventModel): EventDetailsFragment {
            val fragment = EventDetailsFragment()
            val args = Bundle().apply {
                putString("eventTitle", event.title)
                putString("eventSubTitle", event.subTitle)
                putString("eventDescription", event.description)
                putString("eventLocation", event.location)
                putString("eventPrice", event.price)
                putDouble("eventRating", event.rating)
                putInt("eventImage", event.imageRes)
                putBoolean("isFeatured", event.isFeatured)
                putString("category", event.category)
            }
            fragment.arguments = args
            return fragment
        }
    }
}