package com.example.vendiapp

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView

class StallDetailsFragment : Fragment() {

    private lateinit var rvEventPackageDetails: RecyclerView
    private lateinit var packageAdapter: PackageAdapter
    private lateinit var packageList: ArrayList<PackageModel>

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val view = inflater.inflate(R.layout.fragment_stall_details, container, false)

        // Get references to UI elements
        val eventImage: ImageView = view.findViewById(R.id.ivEventImage)
        val eventName: TextView = view.findViewById(R.id.tvEventName)
        val tvSubEventName: TextView = view.findViewById(R.id.tvSubEventName)
        val tvDescription: TextView = view.findViewById(R.id.tvDescription)
        val eventLocation: TextView = view.findViewById(R.id.tvEventLocation)
        val eventPrice: TextView = view.findViewById(R.id.tvEventPrice)
        val eventRating: TextView = view.findViewById(R.id.tvEventRating)

        // Retrieve event data using fragment arguments
        val args = arguments
        if (args != null) {
            val name = args.getString("eventTitle") ?: "Unknown Event"
            val subName = args.getString("eventSubTitle") ?: "Unknown Subtitle"
            val description = args.getString("eventDescription") ?: "Unknown Description"
            val location = args.getString("eventLocation") ?: "Unknown Location"
            val price = args.getString("eventPrice") ?: "No Price Info"
            val rating = args.getDouble("eventRating", 0.0)
            val imageRes = args.getInt("eventImage", R.drawable.img_ezbandph)

            // Debugging Log: Check if values are being received
            println("DEBUG: Fragment Received Data -")
            println("Title: $name")
            println("Subtitle: $subName")
            println("Description: $description")
            println("Location: $location")
            println("Price: $price")
            println("Rating: $rating")
            println("Image: $imageRes")

            // Set data to views
            eventName.text = name
            tvSubEventName.text = subName  // This should now update correctly
            tvDescription.text = description  // This should now update correctly
            eventLocation.text = location
            eventPrice.text = price
            eventRating.text = rating.toString() // Ensure rating updates properly
            eventImage.setImageResource(imageRes)
        } else {
            println("DEBUG: Fragment arguments are null!")
        }

        // Initialize RecyclerView for event packages
        rvEventPackageDetails = view.findViewById(R.id.rvEventPackageDetails)
        rvEventPackageDetails.layoutManager = LinearLayoutManager(requireContext())

        // Sample package data (Modify as needed)
        packageList = arrayListOf(
            PackageModel("Basic Package", "Good for 25 pax", "₱599", R.drawable.img_lemonology),
            PackageModel("Standard Package", "Good for 50 pax", "₱799", R.drawable.img_lemonology_package2),
            PackageModel("Premium Package", "Good for 100 pax", "₱999", R.drawable.img_lemonology_package3)
        )

        // Set adapter
        packageAdapter = PackageAdapter(packageList)
        rvEventPackageDetails.adapter = packageAdapter

        return view
    }

    companion object {
        fun newInstance(
            eventTitle: String,
            eventSubTitle: String,
            eventDescription: String,
            eventLocation: String,
            eventPrice: String,
            eventRating: Double,
            eventImage: Int
        ): StallDetailsFragment {
            val fragment = StallDetailsFragment()
            val args = Bundle().apply {
                putString("eventTitle", eventTitle)
                putString("eventSubTitle", eventSubTitle)
                putString("eventDescription", eventDescription)
                putString("eventLocation", eventLocation)
                putString("eventPrice", eventPrice)
                putDouble("eventRating", eventRating)
                putInt("eventImage", eventImage)
            }
            fragment.arguments = args
            return fragment
        }
    }
}
