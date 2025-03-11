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

class FoodFragment : Fragment() {

    private lateinit var eventAdapter: EventAdapter
    private lateinit var recyclerView: RecyclerView

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val view = inflater.inflate(R.layout.fragment_food, container, false)
        recyclerView = view.findViewById(R.id.rvEventFoodList)

        val allEventFoodList = listOf(
            Event(
                "Carnival Popcorn",
                "Amusement Park Treats",
                "Enjoy a bucket of buttery, freshly popped carnival-style popcorn—perfect for a fun day at the amusement park!",
                "Carnival Grounds, Dagupan",
                "₱99",
                4.5,
                R.drawable.img_carnival_popcorn,
                isFeatured = false
            ),
            Event(
                "Concert Nachos",
                "Live Music Snacks",
                "Crispy nachos topped with gooey cheese, salsa, and jalapeños—your ideal companion for an exciting concert experience!",
                "Concert Venue, Dagupan",
                "₱199",
                4.6,
                R.drawable.img_concert_nachos,
                isFeatured = false
            ),
            Event(
                "Halloween Pumpkin Pie",
                "Spooky Food Fest",
                "Savor the warm flavors of cinnamon, nutmeg, and pumpkin in our special Halloween-themed pumpkin pie!",
                "Halloween Market, Dagupan",
                "₱180",
                4.4,
                R.drawable.img_pumpkin_pie,
                isFeatured = false
            ),
            Event(
                "Christmas Gingerbread",
                "Holiday Market Special",
                "Enjoy the holiday spirit with our soft and spiced gingerbread cookies, decorated with festive icing!",
                "Christmas Village, Dagupan",
                "₱150",
                4.9,
                R.drawable.img_gingerbread,
                isFeatured = false
            )
        )

        // Separate featured & regular food lists
        val foodList = allEventFoodList.filter { !it.isFeatured }

        // Set up the RecyclerView with only non-featured items
        recyclerView.layoutManager = LinearLayoutManager(requireContext())
        eventAdapter = EventAdapter(foodList) { event ->
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
