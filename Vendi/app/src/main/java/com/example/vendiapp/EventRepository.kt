package com.example.vendiapp


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
        Event(
            "Potato Corner",
            "Crispy Flavored Fries",
            "Enjoy the world-famous flavored fries from Potato Corner! Choose from a variety of flavors and experience the ultimate snack-time delight.",
            "Bunuan Guset, Dagupan",
            "₱1299",
            5.0,
            R.drawable.img_potatocorner,
            isFeatured = true
        ),
        Event(
            "Food Festival",
            "A Culinary Adventure",
            "Indulge in a wide selection of street food, gourmet dishes, and sweet treats from different cuisines. A must-visit for food lovers!",
            "City Center",
            "₱999",
            5.0,
            R.drawable.img_food_festival,
            isFeatured = true
        ),
        Event(
            "Festival Street BBQ",
            "Smoky & Savory Grilled Meats",
            "Savor the delicious aroma of grilled meats at the Festival Street BBQ. Enjoy skewers, ribs, and more in an exciting outdoor setting!",
            "Bunuan Guset, Dagupan",
            "₱350",
            4.8,
            R.drawable.img_street_bbq,
            isFeatured = true
        ),
        Event(
            "Food Truck Tacos",
            "Mexican Street Tacos",
            "Taste the bold flavors of Mexico with authentic, freshly made tacos straight from the food truck. Perfect for quick, delicious bites!",
            "Pantal Riverside, Dagupan",
            "₱250",
            4.7,
            R.drawable.img_foodtruck_tacos,
            isFeatured = true
        ),
        Event(
            "BBQ Night",
            "Grill & Chill Experience",
            "Gather with friends and family for a cozy BBQ night. Enjoy premium grilled meats, tasty side dishes, and a lively atmosphere!",
            "Downtown Park, Dagupan",
            "₱1,999",
            4.8,
            R.drawable.img_playerkitchen,
            isFeatured = true
        )
    )


    // Sample Events for Beverages Category
    private fun getBeverageItems(): List<Event> = listOf(
        Event(
            "Lemonology",
            "Non-Alcoholic",
            "From parties to markets and special gatherings, we serve up refreshing, zesty drinks that your guests won’t forget. Ready to add a burst of flavor to your event?",
            "Malued District, Dagupan City",
            "₱799",
            5.0,
            R.drawable.img_lemonology,
            isFeatured = true
        ),
        Event(
            "Coffee Tasting",
            "Artisan Coffee Fair",
            "Discover the finest coffee blends from local and international roasters. Experience unique flavors, learn brewing techniques, and indulge in the ultimate coffee journey.",
            "City Coffee Hub, Manila",
            "₱150",
            4.7,
            R.drawable.img_coffee_tasting,
            isFeatured = true
        ),
        Event(
            "Wine Night",
            "Rooftop Wine & Dine",
            "An elegant evening featuring a curated selection of fine wines, gourmet appetizers, and live acoustic music under the stars. Perfect for wine lovers and social gatherings.",
            "Sky Lounge, Makati",
            "₱180",
            4.8,
            R.drawable.img_wine_night,
            isFeatured = true
        ),
        Event(
            "Oktoberfest Beer",
            "Annual Beer Festival",
            "Join the ultimate beer celebration with unlimited craft beers, live performances, and traditional German cuisine. Get ready for a night of fun and festivities!",
            "BGC Beer Garden, Taguig",
            "₱299",
            4.9,
            R.drawable.img_oktoberfest_beer,
            isFeatured = true
        ),
        Event(
            "Cocktail Mixology",
            "Bartender’s Special",
            "Master the art of cocktail-making with expert bartenders. Learn how to mix, shake, and stir your favorite drinks while enjoying an exclusive tasting session.",
            "The Speakeasy Bar, Quezon City",
            "₱450",
            4.8,
            R.drawable.img_cocktail_mixology,
            isFeatured = true
        )
    )


    // Sample Events for Entertainment Category
    private fun getEntertainmentItems(): List<Event> = listOf(
        Event(
            "EZ Band PH",
            "Live Acoustic Sessions",
            "Experience soulful live performances from EZ Band PH. Perfect for intimate gatherings, corporate events, and special celebrations.",
            "Upang, Dagupan City",
            "₱1299",
            5.0,
            R.drawable.img_ezbandph,
            isFeatured = true
        ),
        Event(
            "Vanenacue",
            "Indie Rock Sensation",
            "Catch the rising indie band Vanenacue as they bring their electrifying sound to the stage. A must-see for music lovers!",
            "Arellano, Dagupan City",
            "₱599",
            5.0,
            R.drawable.img_vanenacue,
            isFeatured = true
        ),
        Event(
            "Stand-up Comedy",
            "Laugh Out Loud Comedy Bar",
            "Get ready for a night of non-stop laughter with the best stand-up comedians in town. Perfect for date nights and group outings!",
            "Downtown Comedy Club, Manila",
            "₱300",
            4.9,
            R.drawable.img_comedy_show,
            isFeatured = true
        ),
        Event(
            "Magic Show",
            "Illusions & Wonders",
            "Step into a world of mystery and wonder with mind-blowing illusions and tricks that will leave you speechless!",
            "Grand Theater, Quezon City",
            "₱220",
            4.6,
            R.drawable.img_magic_show,
            isFeatured = true
        ),
        Event(
            "Rock Concert",
            "City Arena Live",
            "Feel the adrenaline rush with high-energy performances from top rock bands. A night of pure headbanging fun!",
            "City Arena, Makati",
            "₱500",
            4.9,
            R.drawable.img_rock_concert,
            isFeatured = true
        ),
        Event(
            "Circus Spectacular",
            "The Grand Circus Show",
            "A breathtaking showcase of acrobatics, daredevil stunts, and mesmerizing performances for all ages.",
            "Big Top Arena, Taguig",
            "₱400",
            4.7,
            R.drawable.img_circus,
            isFeatured = true
        )
    )

}
