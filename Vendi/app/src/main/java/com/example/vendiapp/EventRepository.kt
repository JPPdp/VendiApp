package com.example.vendiapp.repository

import com.example.vendiapp.R
import com.example.vendiapp.model.Event

class EventRepository {

    // Returns event list based on selected category
    fun getEvents(category: String): List<Event> {
        return when (category) {
            "Food" -> getFoodItems()
            "Beverages" -> getBeverageItems()
            "Entertainment" -> getEntertainmentItems()
            else -> emptyList()
        }
    }

    // Sample Events for Food Category
    private fun getFoodItems(): List<Event> = listOf(
        Event("Festival Street BBQ", "Bunuan Guest, Dagupan", "₱350", 4.8, R.drawable.img_street_bbq, isFeatured = true),
        Event("Food Truck Tacos", "Pantal Riverside, Dagupan", "₱250", 4.7, R.drawable.img_foodtruck_tacos, isFeatured = true),
        Event("Food Festival", "City Center Plaza, Dagupan", "₱999", 5.0, R.drawable.img_potatocorner, isFeatured = true),
        Event("BBQ Night", "Downtown Park, Dagupan", "₱1,999", 4.8, R.drawable.img_playerkitchen, isFeatured = true)
    )

    // Sample Events for Beverages Category
    private fun getBeverageItems(): List<Event> = listOf(
        Event("Coffee Tasting", "Artisan Coffee Fair", "₱150", 4.7, R.drawable.img_coffee_tasting, isFeatured = true),
        Event("Wine Night", "Rooftop Wine & Dine", "₱180", 4.8, R.drawable.img_wine_night, isFeatured = true),
        Event("Oktoberfest Beer", "Annual Beer Festival", "₱299", 4.9, R.drawable.img_oktoberfest_beer, isFeatured = true),
        Event("Cocktail Mixology", "Bartender’s Special", "₱450", 4.8, R.drawable.img_cocktail_mixology, isFeatured = true)
        )

    // Sample Events for Entertainment Category
    private fun getEntertainmentItems(): List<Event> = listOf(
        Event("Stand-up Comedy", "Laugh Out Loud Comedy Bar", "₱300", 4.9, R.drawable.img_comedy_show, isFeatured = true),
        Event("Magic Show", "Illusions & Wonders", "₱220", 4.6, R.drawable.img_magic_show, isFeatured = true),
        Event("Rock Concert", "City Arena Live", "₱500", 4.9, R.drawable.img_rock_concert, isFeatured = true),
        Event("Circus Spectacular", "The Grand Circus Show", "₱400", 4.7, R.drawable.img_circus, isFeatured = true)
        )
}
