package com.example.vendiapp.view

import android.os.Bundle
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import com.example.vendiapp.R
import com.example.vendiapp.viewmodel.EventDetailsViewModel

class EventDetailsActivity : AppCompatActivity() {

    private val viewModel: EventDetailsViewModel by viewModels()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_event_details)

        if (viewModel.event.value == null) {
            intent.extras?.let { bundle ->
                viewModel.loadEventFromBundle(bundle)
            }
        }

        viewModel.event.observe(this) { event ->
            event?.let {
            }
        }

        // Load EventDetailsFragment (it will get data from ViewModel)
        if (savedInstanceState == null) { // Prevent reloading on configuration changes
            supportFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, EventDetailsFragment())
                .commit()
        }
    }
}