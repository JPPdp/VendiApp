package com.example.vendiapp

import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.vendiapp.model.Event


class EntertainmentFragment : Fragment() {

    private lateinit var eventAdapter: EventAdapter
    private lateinit var recyclerView: RecyclerView

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val view = inflater.inflate(R.layout.fragment_entertainment, container, false)
        recyclerView = view.findViewById(R.id.rvEventEntertainmentList)

        // Sample Event Entertainment Data (Including both regular & featured)
        val allEventEntertainmentList = listOf(
            Event("Live Jazz Night", "Downtown Jazz Club", "₱250", 4.8, R.drawable.img_jazz_night, isFeatured = true),
            Event("Stand-up Comedy", "Laugh Out Loud Comedy Bar", "₱300", 4.9, R.drawable.img_comedy_show, isFeatured = true),
            Event("Outdoor Movie Night", "Park Cinema Experience", "₱180", 4.7, R.drawable.img_movie_night, isFeatured = false),
            Event("Magic Show", "Illusions & Wonders", "₱220", 4.6, R.drawable.img_magic_show, isFeatured = true),
            Event("Rock Concert", "City Arena Live", "₱500", 4.9, R.drawable.img_rock_concert, isFeatured = true),
            Event("K-Pop Dance Workshop", "Learn from the Pros", "₱350", 4.8, R.drawable.img_kpop_dance, isFeatured = false),
            Event("Circus Spectacular", "The Grand Circus Show", "₱400", 4.7, R.drawable.img_circus, isFeatured = true),
            Event("Theater Play: Romeo & Juliet", "Classic Drama Revival", "₱280", 4.8, R.drawable.img_theater_play, isFeatured = false),
            Event("Gaming Tournament", "Esports Battle Arena", "₱150", 4.6, R.drawable.img_gaming_tournament, isFeatured = false)
        )

        // Separate featured & regular food lists
        val foodList = allEventEntertainmentList.filter { !it.isFeatured }

        // Set up the RecyclerView with only non-featured items
        recyclerView.layoutManager = LinearLayoutManager(requireContext())
        eventAdapter = EventAdapter(foodList)
        recyclerView.adapter = eventAdapter

        return view
    }

}