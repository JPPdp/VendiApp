package com.example.vendiapp

import android.content.Intent
import android.os.Bundle
import android.util.Log
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView

class EntertainmentFragment : Fragment() {

    private lateinit var eventAdapter: EventAdapter
    private lateinit var recyclerView: RecyclerView

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val view = inflater.inflate(R.layout.fragment_entertainment, container, false)
        recyclerView = view.findViewById(R.id.rvEventEntertainmentList)

        val allEventEntertainmentList = listOf(
            Event(
                "Outdoor Movie Night",
                "Park Cinema Experience",
                "Enjoy a cozy outdoor movie night under the stars with family and friends. Bring your blankets and snacks!",
                "City Park, Dagupan",
                "₱180",
                4.7,
                R.drawable.img_movie_night,
                isFeatured = false
            ),
            Event(
                "K-Pop Dance Workshop",
                "Learn from the Pros",
                "Join professional K-Pop choreographers and learn the latest dance moves from your favorite idols!",
                "Dance Studio, Dagupan",
                "₱350",
                4.8,
                R.drawable.img_kpop_dance,
                isFeatured = false
            ),
            Event(
                "Theater Play: Romeo & Juliet",
                "Classic Drama Revival",
                "Experience Shakespeare’s timeless love story brought to life on stage with stunning performances.",
                "Cultural Center, Dagupan",
                "₱280",
                4.8,
                R.drawable.img_theater_play,
                isFeatured = false
            ),
            Event(
                "Gaming Tournament",
                "Esports Battle Arena",
                "Compete in an intense gaming tournament and prove your skills in the ultimate battle arena!",
                "Cyber Arena, Dagupan",
                "₱150",
                4.6,
                R.drawable.img_gaming_tournament,
                isFeatured = false
            )
        )


        // Separate regular (non-featured) entertainment events
        val entertainmentList = allEventEntertainmentList.filter { !it.isFeatured }

        // Set up the RecyclerView with only non-featured items
        recyclerView.layoutManager = LinearLayoutManager(requireContext())
        eventAdapter = EventAdapter(entertainmentList) { event: Event ->
            val intent = Intent(requireContext(), StallDetailsActivity::class.java).apply {
                putExtra("eventTitle", event.title)
                putExtra("eventSubTitle", event.subTitle)
                putExtra("eventDescription", event.description)
                putExtra("eventLocation", event.location)
                putExtra("eventPrice", event.price)
                putExtra("eventRating", event.rating)
                putExtra("eventImage", event.imageRes)
            }

            // Debugging log to check if data is passed correctly
            Log.d("DEBUG", "Passing data - Title: ${event.title}, Rating: ${event.rating}")

            startActivity(intent)
        }
        recyclerView.adapter = eventAdapter

        return view
    }
}
