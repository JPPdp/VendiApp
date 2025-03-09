package com.example.vendiapp

import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.vendiapp.model.Event

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
            Event("Festival Street BBQ", "Grill & Chill Festival", "₱350", 4.8, R.drawable.img_street_bbq, isFeatured = true),
            Event("Carnival Popcorn", "Amusement Park Treats", "₱99", 4.5, R.drawable.img_carnival_popcorn, isFeatured = false),
            Event("Concert Nachos", "Live Music Snacks", "₱199", 4.6, R.drawable.img_concert_nachos, isFeatured = false),
            Event("Food Truck Tacos", "Weekend Food Fair", "₱250", 4.7, R.drawable.img_foodtruck_tacos, isFeatured = true),
            Event("Halloween Pumpkin Pie", "Spooky Food Fest", "₱180", 4.4, R.drawable.img_pumpkin_pie, isFeatured = false),
            Event("Christmas Gingerbread", "Holiday Market Special", "₱150", 4.9, R.drawable.img_gingerbread, isFeatured = false),
            Event("Food Festival", "City Center", "₱999", 5.0, R.drawable.img_potatocorner, isFeatured = true),
            Event("BBQ Night", "Downtown", "₱1,999", 4.8, R.drawable.img_playerkitchen, isFeatured = true)
        )

        // Separate featured & regular food lists
        val foodList = allEventFoodList.filter { !it.isFeatured }

        // Set up the RecyclerView with only non-featured items
        recyclerView.layoutManager = LinearLayoutManager(requireContext())
        eventAdapter = EventAdapter(foodList)
        recyclerView.adapter = eventAdapter

        return view
    }
}
