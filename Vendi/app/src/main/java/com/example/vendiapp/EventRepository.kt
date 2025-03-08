package com.example.vendiapp.repository

import com.example.vendiapp.R
import com.example.vendiapp.model.Event

class EventRepository {

    fun getEvents(category: String): List<Event> {
        return when (category) {
            "Food" -> getFoodItems()
            "Beverages" -> getBeverageItems()
            "Entertainment" -> getEntertainmentItems()
            else -> emptyList()
        }
    }

    private fun getFoodItems(): List<Event> = listOf(
        Event("Food Festival", "City Center", "₱999", 5.0, R.drawable.img_potatocorner, isFeatured = true),
        Event("BBQ Night", "Downtown", "₱1,999", 4.8, R.drawable.img_playerkitchen, isFeatured = true)
    )

    private fun getBeverageItems(): List<Event> = listOf(
        Event("Coffee Tasting", "Cafe Lounge", "₱150", 4.6, R.drawable.img_coffe, isFeatured = true),
        Event("Wine Night", "Rooftop Bar", "₱180", 4.7, R.drawable.img_fruitshake, isFeatured = true)
    )

    private fun getEntertainmentItems(): List<Event> = listOf(
        Event("Concert Night", "Arena", "₱3,000", 4.9, R.drawable.img_concert, isFeatured = true),
        Event("Movie Screening", "Theater", "₱500", 4.5, R.drawable.img_movie, isFeatured = true)
    )
}
