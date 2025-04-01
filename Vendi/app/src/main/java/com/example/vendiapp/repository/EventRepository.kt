package com.example.vendiapp.repository

import com.example.vendiapp.model.VendorModel


class EventRepository {

    // Returns event list based on selected category
    fun getEvents(category: String): List<VendorModel> {

        return when (category) {
            "Food" -> getFoodItems()
            "Beverages" -> getBeverageItems()
            "Entertainment" -> getEntertainmentItems()
            "All" -> getFoodItems() + getBeverageItems() + getEntertainmentItems() // Fetch all
            else -> emptyList()
        }
    }

    // Sample Events for Food Category
    private fun getFoodItems(): List<VendorModel> = listOf(
        VendorModel(1,
            "Potato Corner",
            "Crispy Flavored Fries",
            "Enjoy the world-famous flavored fries from Potato Corner! Choose from a variety of flavors and experience the ultimate snack-time delight.",
            "Bunuan Guset, Dagupan",
            "₱1299",
            5.0,
            //R.drawable.img_potatocorner,
            isFeatured = true, "food"
        )
        )


    // Sample Events for Beverages Category
    private fun getBeverageItems(): List<VendorModel> = listOf(
        VendorModel(
            2,
            "Lemonology",
            "Non-Alcoholic",
            "From parties to markets and special gatherings, we serve up refreshing, zesty drinks that your guests won’t forget. Ready to add a burst of flavor to your event?",
            "Malued District, Dagupan City",
            "₱799",
            5.0,
            //R.drawable.img_lemonology,
            isFeatured = true, "beverages"
        )
    )


    // Sample Events for Entertainment Category
    private fun getEntertainmentItems(): List<VendorModel> = listOf(
        VendorModel(
            3,
            "EZ Band PH",
            "Live Acoustic Sessions",
            "Experience soulful live performances from EZ Band PH. Perfect for intimate gatherings, corporate events, and special celebrations.",
            "Upang, Dagupan City",
            "₱1299",
            5.0,
            //R.drawable.img_ezbandph,
            isFeatured = true, "entertainment"
        )
    )
}