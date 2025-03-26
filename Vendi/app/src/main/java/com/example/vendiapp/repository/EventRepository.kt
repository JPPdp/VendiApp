package com.example.vendiapp.repository

import android.util.Log
import com.example.vendiapp.api.ApiClient
import com.example.vendiapp.model.EventModel
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class EventRepository {

    // Returns event list based on selected category
    fun getEvents(category: String): List<EventModel> {
        return when (category) {
            "1" -> getFoodItems()
            "2" -> getBeverageItems()
            "3" -> getEntertainmentItems()
            "All" -> getAllEvents() // Fetch all categories
            else -> emptyList()
        }
    }

    // Fetch Food Items from API
    private fun getFoodItems(): List<EventModel> {
        val foodItems = mutableListOf<EventModel>()

        ApiClient.eventApiService.getEvents("1").enqueue(object : Callback<List<EventModel>> {
            override fun onResponse(call: Call<List<EventModel>>, response: Response<List<EventModel>>) {
                if (response.isSuccessful) {
                    response.body()?.let { events ->
                        foodItems.addAll(events)
                    }
                } else {
                    Log.e("API_ERROR", "Error fetching Food items: ${response.code()}")
                }
            }

            override fun onFailure(call: Call<List<EventModel>>, t: Throwable) {
                Log.e("API_ERROR", "Failed to load Food items: ${t.message}")
            }
        })

        return foodItems
    }

    // Fetch Beverage Items from API
    private fun getBeverageItems(): List<EventModel> {
        val beverageItems = mutableListOf<EventModel>()

        ApiClient.eventApiService.getEvents("2").enqueue(object : Callback<List<EventModel>> {
            override fun onResponse(call: Call<List<EventModel>>, response: Response<List<EventModel>>) {
                if (response.isSuccessful) {
                    response.body()?.let { events ->
                        beverageItems.addAll(events)
                    }
                } else {
                    Log.e("API_ERROR", "Error fetching Beverage items: ${response.code()}")
                }
            }

            override fun onFailure(call: Call<List<EventModel>>, t: Throwable) {
                Log.e("API_ERROR", "Failed to load Beverage items: ${t.message}")
            }
        })

        return beverageItems
    }

    // Fetch Entertainment Items from API
    private fun getEntertainmentItems(): List<EventModel> {
        val entertainmentItems = mutableListOf<EventModel>()

        ApiClient.eventApiService.getEvents("3").enqueue(object : Callback<List<EventModel>> {
            override fun onResponse(call: Call<List<EventModel>>, response: Response<List<EventModel>>) {
                if (response.isSuccessful) {
                    response.body()?.let { events ->
                        entertainmentItems.addAll(events)
                    }
                } else {
                    Log.e("API_ERROR", "Error fetching Entertainment items: ${response.code()}")
                }
            }

            override fun onFailure(call: Call<List<EventModel>>, t: Throwable) {
                Log.e("API_ERROR", "Failed to load Entertainment items: ${t.message}")
            }
        })

        return entertainmentItems
    }

    // Fetch All Events (combines all categories)
    private fun getAllEvents(): List<EventModel> {
        val allEvents = mutableListOf<EventModel>()
        allEvents.addAll(getFoodItems())
        allEvents.addAll(getBeverageItems())
        allEvents.addAll(getEntertainmentItems())
        return allEvents
    }
}

/*import com.example.vendiapp.R
import com.example.vendiapp.model.EventModel


class EventRepository {

    // Returns event list based on selected category
    fun getEvents(category: String): List<EventModel> {

        return when (category) {
            "Food" -> getFoodItems()
            "Beverages" -> getBeverageItems()
            "Entertainment" -> getEntertainmentItems()
            "All" -> getFoodItems() + getBeverageItems() + getEntertainmentItems() // Fetch all
            else -> emptyList()
        }
    }

    // Sample Events for Food Category
    private fun getFoodItems(): List<EventModel> = listOf(
        EventModel(
            "Potato Corner",
            "Crispy Flavored Fries",
            "Enjoy the world-famous flavored fries from Potato Corner! Choose from a variety of flavors and experience the ultimate snack-time delight.",
            "Bunuan Guset, Dagupan",
            "₱1299",
            5.0,
            R.drawable.img_potatocorner,
            isFeatured = true, "food"
        ),
        EventModel(
            "Food Festival",
            "A Culinary Adventure",
            "Indulge in a wide selection of street food, gourmet dishes, and sweet treats from different cuisines. A must-visit for food lovers!",
            "City Center",
            "₱999",
            5.0,
            R.drawable.img_food_festival,
            isFeatured = true, "food"
        ),
        EventModel(
            "Festival Street BBQ",
            "Smoky & Savory Grilled Meats",
            "Savor the delicious aroma of grilled meats at the Festival Street BBQ. Enjoy skewers, ribs, and more in an exciting outdoor setting!",
            "Bunuan Guset, Dagupan",
            "₱350",
            4.8,
            R.drawable.img_street_bbq,
            isFeatured = true, "food"
        ),
        EventModel(
            "Food Truck Tacos",
            "Mexican Street Tacos",
            "Taste the bold flavors of Mexico with authentic, freshly made tacos straight from the food truck. Perfect for quick, delicious bites!",
            "Pantal Riverside, Dagupan",
            "₱250",
            4.7,
            R.drawable.img_foodtruck_tacos,
            isFeatured = true, "food"
        ),
        EventModel(
            "BBQ Night",
            "Grill & Chill Experience",
            "Gather with friends and family for a cozy BBQ night. Enjoy premium grilled meats, tasty side dishes, and a lively atmosphere!",
            "Downtown Park, Dagupan",
            "₱1,999",
            4.8,
            R.drawable.img_playerkitchen,
            isFeatured = true, "food"
        ),
        EventModel("Carnival Popcorn", "Amusement Park Treats", "Enjoy a bucket of buttery, freshly popped carnival-style popcorn.", "Carnival Grounds, Dagupan", "₱99", 4.5,
            R.drawable.img_carnival_popcorn, false, "food"),
        EventModel("Concert Nachos", "Live Music Snacks", "Crispy nachos topped with gooey cheese, salsa, and jalapeños.", "Concert Venue, Dagupan", "₱199", 4.6,
            R.drawable.img_concert_nachos, false, "food"),
        EventModel("Halloween Pumpkin Pie", "Spooky Food Fest", "Savor the warm flavors of cinnamon, nutmeg, and pumpkin.", "Halloween Market, Dagupan", "₱180", 4.4,
            R.drawable.img_pumpkin_pie, false, "food"),
        EventModel("Christmas Gingerbread", "Holiday Market Special", "Soft and spiced gingerbread cookies with festive icing.", "Christmas Village, Dagupan", "₱150", 4.9,
            R.drawable.img_gingerbread, false, "food"),
    )


    // Sample Events for Beverages Category
    private fun getBeverageItems(): List<EventModel> = listOf(
        EventModel(
            "Lemonology",
            "Non-Alcoholic",
            "From parties to markets and special gatherings, we serve up refreshing, zesty drinks that your guests won’t forget. Ready to add a burst of flavor to your event?",
            "Malued District, Dagupan City",
            "₱799",
            5.0,
            R.drawable.img_lemonology,
            isFeatured = true, "beverages"
        ),
        EventModel(
            "Coffee Tasting",
            "Artisan Coffee Fair",
            "Discover the finest coffee blends from local and international roasters. Experience unique flavors, learn brewing techniques, and indulge in the ultimate coffee journey.",
            "City Coffee Hub, Manila",
            "₱150",
            4.7,
            R.drawable.img_coffee_tasting,
            isFeatured = true, "beverages"
        ),
        EventModel(
            "Wine Night",
            "Rooftop Wine & Dine",
            "An elegant evening featuring a curated selection of fine wines, gourmet appetizers, and live acoustic music under the stars. Perfect for wine lovers and social gatherings.",
            "Sky Lounge, Makati",
            "₱180",
            4.8,
            R.drawable.img_wine_night,
            isFeatured = true, "beverages"
        ),
        EventModel(
            "Oktoberfest Beer",
            "Annual Beer Festival",
            "Join the ultimate beer celebration with unlimited craft beers, live performances, and traditional German cuisine. Get ready for a night of fun and festivities!",
            "BGC Beer Garden, Taguig",
            "₱299",
            4.9,
            R.drawable.img_oktoberfest_beer,
            isFeatured = true, "beverages"
        ),
        EventModel(
            "Cocktail Mixology",
            "Bartender’s Special",
            "Master the art of cocktail-making with expert bartenders. Learn how to mix, shake, and stir your favorite drinks while enjoying an exclusive tasting session.",
            "The Speakeasy Bar, Quezon City",
            "₱450",
            4.8,
            R.drawable.img_cocktail_mixology,
            isFeatured = true, "beverages"
        ),
        EventModel(
            "Summer Lemonade",
            "Beachfront Refreshments",
            "Beat the heat with our signature homemade lemonade! Freshly squeezed and served ice-cold for the perfect summer refreshment.",
            "Seaside Market",
            "₱99",
            4.5,
            R.drawable.img_summer_lemonade,
            isFeatured = false, "beverages"
        ),
        EventModel(
            "Matcha Latte",
            "Japanese Tea House",
            "Experience the smooth, earthy flavors of authentic matcha latte, crafted with the finest green tea powder and creamy steamed milk.",
            "Zen Garden Café",
            "₱170",
            4.6,
            R.drawable.img_matcha_latte,
            isFeatured = false, "beverages"
        ),
        EventModel(
            "Bubble Tea Fiesta",
            "Boba Lovers' Meet",
            "Indulge in a variety of bubble tea flavors with chewy tapioca pearls and exciting toppings. A must-visit for all boba enthusiasts!",
            "Boba Junction",
            "₱120",
            4.7,
            R.drawable.img_bubble_tea,
            isFeatured = false, "beverages"
        ),
        EventModel(
            "Hot Chocolate Delight",
            "Winter Market Warmers",
            "Warm up with a cup of rich, velvety hot chocolate topped with whipped cream and marshmallows. A cozy treat for chilly evenings.",
            "Winter Wonderland Fair",
            "₱130",
            4.6,
            R.drawable.img_hot_chocolate,
            isFeatured = false, "beverages"
        )

    )


    // Sample Events for Entertainment Category
    private fun getEntertainmentItems(): List<EventModel> = listOf(
        EventModel(
            "EZ Band PH",
            "Live Acoustic Sessions",
            "Experience soulful live performances from EZ Band PH. Perfect for intimate gatherings, corporate events, and special celebrations.",
            "Upang, Dagupan City",
            "₱1299",
            5.0,
            R.drawable.img_ezbandph,
            isFeatured = true, "entertainment"
        ),
        EventModel(
            "Vanenacue",
            "Indie Rock Sensation",
            "Catch the rising indie band Vanenacue as they bring their electrifying sound to the stage. A must-see for music lovers!",
            "Arellano, Dagupan City",
            "₱599",
            5.0,
            R.drawable.img_vanenacue,
            isFeatured = true, "entertainment"
        ),
        EventModel(
            "Stand-up Comedy",
            "Laugh Out Loud Comedy Bar",
            "Get ready for a night of non-stop laughter with the best stand-up comedians in town. Perfect for date nights and group outings!",
            "Downtown Comedy Club, Manila",
            "₱300",
            4.9,
            R.drawable.img_comedy_show,
            isFeatured = true, "entertainment"
        ),
        EventModel(
            "Magic Show",
            "Illusions & Wonders",
            "Step into a world of mystery and wonder with mind-blowing illusions and tricks that will leave you speechless!",
            "Grand Theater, Quezon City",
            "₱220",
            4.6,
            R.drawable.img_magic_show,
            isFeatured = true, "entertainment"
        ),
        EventModel(
            "Rock Concert",
            "City Arena Live",
            "Feel the adrenaline rush with high-energy performances from top rock bands. A night of pure headbanging fun!",
            "City Arena, Makati",
            "₱500",
            4.9,
            R.drawable.img_rock_concert,
            isFeatured = true, "entertainment"
        ),
        EventModel(
            "Circus Spectacular",
            "The Grand Circus Show",
            "A breathtaking showcase of acrobatics, daredevil stunts, and mesmerizing performances for all ages.",
            "Big Top Arena, Taguig",
            "₱400",
            4.7,
            R.drawable.img_circus,
            isFeatured = true, "entertainment"
        ),
        EventModel("Outdoor Movie Night", "Park Cinema Experience", "Enjoy a cozy outdoor movie night under the stars.", "City Park, Dagupan", "₱180", 4.7,
            R.drawable.img_movie_night, false, "entertainment"),
        EventModel("K-Pop Dance Workshop", "Learn from the Pros", "Join professional K-Pop choreographers and learn the latest moves.", "Dance Studio, Dagupan", "₱350", 4.8,
            R.drawable.img_kpop_dance, false, "entertainment"),
        EventModel("Theater Play: Romeo & Juliet", "Classic Drama Revival", "Experience Shakespeare’s timeless love story on stage.", "Cultural Center, Dagupan", "₱280", 4.8,
            R.drawable.img_theater_play, false, "entertainment"),
        EventModel("Gaming Tournament", "Esports Battle Arena", "Compete in an intense gaming tournament!", "Cyber Arena, Dagupan", "₱150", 4.6,
            R.drawable.img_gaming_tournament, false, "entertainment")
    )


}*/