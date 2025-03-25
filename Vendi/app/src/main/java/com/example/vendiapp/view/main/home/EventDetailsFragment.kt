package com.example.vendiapp.view.main.home

import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.*
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.example.vendiapp.R
import com.example.vendiapp.model.EventModel
import com.example.vendiapp.view.main.ChatFragment

class EventDetailsFragment : Fragment() {

    private var vendorId: Int = 0
    private var eventId: Int = 0

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?
    ): View {
        val view = inflater.inflate(R.layout.fragment_event_details, container, false)

        // Initialize UI elements
        val eventImage: ImageView = view.findViewById(R.id.ivEventImage)
        val eventName: TextView = view.findViewById(R.id.tvEventName)
        val eventLocation: TextView = view.findViewById(R.id.tvEventLocation)
        val eventPrice: TextView = view.findViewById(R.id.tvEventPrice)
        val btnChatWithVendor: Button = view.findViewById(R.id.btnChatWithVendor)

        // Load event data from arguments
        arguments?.let { bundle ->
            eventId = bundle.getInt("eventId", 0)
            vendorId = bundle.getInt("vendorId", 0)
            eventName.text = bundle.getString("eventTitle", "")
            eventLocation.text = bundle.getString("eventLocation", "")
            eventPrice.text = bundle.getString("eventPrice", "")

            Glide.with(this)
                .load(bundle.getString("eventImage", ""))
                .placeholder(R.drawable.baseline_cloud_download_24)
                .into(eventImage)

            Log.d("EventDetailsFragment", "✅ Vendor ID: $vendorId, Event ID: $eventId")
        }

        // Open chat with vendor
        btnChatWithVendor.setOnClickListener {
            openChat(vendorId)
        }

        return view
    }

    // Open Chat with Vendor
    private fun openChat(vendorId: Int) {
        if (vendorId != 0) {
            val chatFragment = ChatFragment.newInstance(vendorId)
            parentFragmentManager.beginTransaction()
                .replace(R.id.fragment_container, chatFragment)
                .addToBackStack(null)
                .commit()
        } else {
            Toast.makeText(requireContext(), "❌ Invalid vendor data. Cannot open chat.", Toast.LENGTH_SHORT).show()
        }
    }

    // Create a new instance of EventDetailsFragment with event data
    companion object {
        fun newInstance(event: EventModel): EventDetailsFragment {
            return EventDetailsFragment().apply {
                arguments = Bundle().apply {
                    putInt("eventId", event.id)
                    putString("eventTitle", event.title)
                    putString("eventLocation", event.location)
                    putString("eventPrice", event.price)
                    putString("eventImage", event.imageUrl)
                    putInt("vendorId", event.vendorId)  // ✅ Ensure vendorId is passed correctly
                }
            }
        }
    }
}
