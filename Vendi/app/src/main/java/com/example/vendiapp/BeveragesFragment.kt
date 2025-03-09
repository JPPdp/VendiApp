package com.example.vendiapp

import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.vendiapp.model.Event


class BeveragesFragment : Fragment() {

    private lateinit var eventAdapter: EventAdapter
    private lateinit var recyclerView: RecyclerView

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val view = inflater.inflate(R.layout.fragment_beverages, container, false)
        recyclerView = view.findViewById(R.id.rvEventBeveragesList)

        // Sample Event beverages Data (Including both regular & featured)
        val allEventBeveragesList = listOf(
            Event("Coffee Tasting", "Artisan Coffee Fair", "₱150", 4.7, R.drawable.img_coffee_tasting, isFeatured = true),
            Event("Wine Night", "Rooftop Wine & Dine", "₱180", 4.8, R.drawable.img_wine_night, isFeatured = true),
            Event("Summer Lemonade", "Beachfront Refreshments", "₱99", 4.5, R.drawable.img_summer_lemonade, isFeatured = false),
            Event("Oktoberfest Beer", "Annual Beer Festival", "₱299", 4.9, R.drawable.img_oktoberfest_beer, isFeatured = true),
            Event("Matcha Latte", "Japanese Tea House", "₱170", 4.6, R.drawable.img_matcha_latte, isFeatured = false),
            Event("Bubble Tea Fiesta", "Boba Lovers' Meet", "₱120", 4.7, R.drawable.img_bubble_tea, isFeatured = false),
            Event("Cocktail Mixology", "Bartender’s Special", "₱450", 4.8, R.drawable.img_cocktail_mixology, isFeatured = true),
            Event("Hot Chocolate Delight", "Winter Market Warmers", "₱130", 4.6, R.drawable.img_hot_chocolate, isFeatured = false)
        )

        // Separate featured & regular food lists
        val foodList = allEventBeveragesList.filter { !it.isFeatured }

        // Set up the RecyclerView with only non-featured items
        recyclerView.layoutManager = LinearLayoutManager(requireContext())
        eventAdapter = EventAdapter(foodList)
        recyclerView.adapter = eventAdapter

        return view
    }

}