package com.example.vendiapp

import android.content.Intent
import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView

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
            Event(
                "Summer Lemonade",
                "Beachfront Refreshments",
                "Beat the heat with our signature homemade lemonade! Freshly squeezed and served ice-cold for the perfect summer refreshment.",
                "Seaside Market",
                "₱99",
                4.5,
                R.drawable.img_summer_lemonade,
                isFeatured = false
            ),
            Event(
                "Matcha Latte",
                "Japanese Tea House",
                "Experience the smooth, earthy flavors of authentic matcha latte, crafted with the finest green tea powder and creamy steamed milk.",
                "Zen Garden Café",
                "₱170",
                4.6,
                R.drawable.img_matcha_latte,
                isFeatured = false
            ),
            Event(
                "Bubble Tea Fiesta",
                "Boba Lovers' Meet",
                "Indulge in a variety of bubble tea flavors with chewy tapioca pearls and exciting toppings. A must-visit for all boba enthusiasts!",
                "Boba Junction",
                "₱120",
                4.7,
                R.drawable.img_bubble_tea,
                isFeatured = false
            ),
            Event(
                "Hot Chocolate Delight",
                "Winter Market Warmers",
                "Warm up with a cup of rich, velvety hot chocolate topped with whipped cream and marshmallows. A cozy treat for chilly evenings.",
                "Winter Wonderland Fair",
                "₱130",
                4.6,
                R.drawable.img_hot_chocolate,
                isFeatured = false
            )
        )

        // Separate regular (non-featured) beverage events
        val beveragesList = allEventBeveragesList.filter { !it.isFeatured }

        // Set up the RecyclerView with only non-featured items
        recyclerView.layoutManager = LinearLayoutManager(requireContext())
        eventAdapter = EventAdapter(beveragesList) { event: Event ->
            val intent = Intent(requireContext(), StallDetailsActivity::class.java).apply {
                putExtra("eventTitle", event.title)
                putExtra("eventSubTitle", event.subTitle)
                putExtra("eventDescription", event.description)
                putExtra("eventLocation", event.location)
                putExtra("eventPrice", event.price)
                putExtra("eventRating", event.rating)
                putExtra("eventImage", event.imageRes)
            }
            startActivity(intent)
        }
        recyclerView.adapter = eventAdapter

        return view
    }
}
