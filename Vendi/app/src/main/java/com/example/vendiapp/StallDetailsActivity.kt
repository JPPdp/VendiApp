package com.example.vendiapp

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity

class StallDetailsActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_event_details)

        // Retrieve event details from intent
        val eventTitle = intent.getStringExtra("eventTitle") ?: "Unknown Title Event"
        val eventSubTitle = intent.getStringExtra("eventSubTitle") ?: "Unknown Subtitle Event"
        val eventDescription = intent.getStringExtra("eventDescription") ?: "Unknown Description Event"
        val eventLocation = intent.getStringExtra("eventLocation") ?: "Unknown Location"
        val eventPrice = intent.getStringExtra("eventPrice") ?: "No Price Info"
        val eventRating = intent.getDoubleExtra("eventRating", 0.0)
        val eventImage = intent.getIntExtra("eventImage", R.drawable.img_ezbandph)

        //  Debug Log: Check if data is retrieved from Intent
        println("DEBUG: Passing data - Title: $eventTitle, Subtitle: $eventSubTitle, Description: $eventDescription, Location: $eventLocation, Price: $eventPrice, Rating: $eventRating, Image: $eventImage")

        // Pass event details as arguments to StallDetailsFragment
        val fragment = StallDetailsFragment().apply {
            arguments = Bundle().apply {
                putString("eventTitle", eventTitle)
                putString("eventSubTitle", eventSubTitle)
                putString("eventDescription", eventDescription)
                putString("eventLocation", eventLocation)
                putString("eventPrice", eventPrice)
                putDouble("eventRating", eventRating)
                putInt("eventImage", eventImage)
            }
        }

        // Load StallDetailsFragment with arguments
        supportFragmentManager.beginTransaction()
            .replace(R.id.fgtContainer, fragment)
            .commit()
    }
}
