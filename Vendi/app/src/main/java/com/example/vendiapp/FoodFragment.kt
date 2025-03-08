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

    private lateinit var foodAdapter: EventAdapter
    private lateinit var recyclerView: RecyclerView

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val view = inflater.inflate(R.layout.fragment_food, container, false)
        recyclerView = view.findViewById(R.id.rvFoodList)

        // Sample Food Data (Including both regular & featured)
        val allFoodList = listOf(
            Event("Spicy Ramen", "Japanese Delight", "₱299", 4.7, R.drawable.img_spicyramen, isFeatured = false),
            Event("Cheese Burger", "American Classic", "₱249", 4.5, R.drawable.img_cheeseburger, isFeatured = false),
            Event("Sushi Platter", "Fresh & Tasty", "₱899", 4.9, R.drawable.img_sushiplatter, isFeatured = false),
            Event("BBQ Chicken", "Smokey Goodness", "₱350", 4.6, R.drawable.img_bbqchicken, isFeatured = false)
        )

        // Separate featured & regular food lists
        val foodList = allFoodList.filter { !it.isFeatured }

        // Set up the RecyclerView with only non-featured items
        recyclerView.layoutManager = LinearLayoutManager(requireContext())
        foodAdapter = EventAdapter(foodList)
        recyclerView.adapter = foodAdapter

        return view
    }
}
