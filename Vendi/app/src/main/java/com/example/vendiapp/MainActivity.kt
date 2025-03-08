package com.example.vendiapp

import android.content.res.ColorStateList
import android.os.Bundle
import android.widget.ImageView
import android.widget.TextView
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import androidx.viewpager2.widget.ViewPager2
import com.example.vendiapp.viewmodel.EventViewModel
import com.google.android.material.bottomnavigation.BottomNavigationView
import com.google.android.material.tabs.TabLayout
import com.google.android.material.tabs.TabLayoutMediator

class MainActivity : AppCompatActivity() {

    private lateinit var featuredAdapter: EventAdapter // Adapter for featured events
    private val eventViewModel: EventViewModel by viewModels() // ViewModel to manage event data

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        // Initialize UI Components
        val tabLayout: TabLayout = findViewById(R.id.tabLayout)
        val viewPager: ViewPager2 = findViewById(R.id.viewPager)
        val featuredRecyclerView: RecyclerView = findViewById(R.id.rvFeaturedProducts)

        // Setup Featured Events RecyclerView (Horizontal Layout)
        featuredRecyclerView.layoutManager = LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false)
        featuredAdapter = EventAdapter(emptyList()) // Initially, the list is empty
        featuredRecyclerView.adapter = featuredAdapter

        // Observe ViewModel to update RecyclerView when event data changes
        eventViewModel.events.observe(this) { newList ->
            featuredAdapter.updateData(newList) // Update adapter with new event data
        }

        // Setup ViewPager2 with Tabs
        val adapter = ViewPagerAdapter(this)
        viewPager.adapter = adapter
        viewPager.isUserInputEnabled = false // Disable swipe to prevent accidental switching

        // Ensure the first tab (Food) is selected when the app launches
        viewPager.setCurrentItem(0, false) // Set default tab to "Food"
        tabLayout.selectTab(tabLayout.getTabAt(0))

        // Customize Tab Layout with Icons and Text
        val tabIcons = listOf(
            R.drawable.icon_noodle_white,
            R.drawable.icon_drink_black,
            R.drawable.icon_chair_grey
        )
        val tabTexts = listOf("Food", "Beverages", "Entertainment")

        // Link TabLayout with ViewPager2 using TabLayoutMediator
        TabLayoutMediator(tabLayout, viewPager) { tab, position ->
            val tabView = layoutInflater.inflate(R.layout.custom_tab, null)
            val tabIcon = tabView.findViewById<ImageView>(R.id.tab_icon)
            val tabText = tabView.findViewById<TextView>(R.id.tab_text)

            tabIcon.setImageResource(tabIcons[position]) // Set tab icon
            tabText.text = tabTexts[position] // Set tab text
            tab.customView = tabView
        }.attach()

        // Ensure the first tab is styled correctly on startup
        updateTabAppearance(tabLayout.getTabAt(0), isSelected = true)

        // Handle Tab Selection
        tabLayout.addOnTabSelectedListener(object : TabLayout.OnTabSelectedListener {
            override fun onTabSelected(tab: TabLayout.Tab?) {
                updateTabAppearance(tab, isSelected = true)
                viewPager.setCurrentItem(tab?.position ?: 0, false) // Switch ViewPager page

                // Update events based on selected category
                val category = when (tab?.position) {
                    0 -> "Food"
                    1 -> "Beverages"
                    2 -> "Entertainment"
                    else -> "Food"
                }
                eventViewModel.loadEvents(category) // Load events for selected category
            }

            override fun onTabUnselected(tab: TabLayout.Tab?) {
                updateTabAppearance(tab, isSelected = false)
            }

            override fun onTabReselected(tab: TabLayout.Tab?) {
                // Do nothing on reselection
            }
        })

        // Setup Bottom Navigation
        val bottomNavigationView = findViewById<BottomNavigationView>(R.id.bottomNavigationView)

        // Load the default fragment (HomeFragment) on app launch
        loadFragment(HomeFragment())

        // Handle bottom navigation item selection
        bottomNavigationView.setOnItemSelectedListener { item ->
            when (item.itemId) {
                R.id.nav_home -> loadFragment(HomeFragment()) // Load Home Fragment
                R.id.nav_schedule -> loadFragment(ScheduleFragment()) // Load Schedule Fragment
                R.id.nav_profile -> loadFragment(ProfileFragment()) // Load Profile Fragment
            }
            true
        }

        // Load default category events (Food) on app launch
        eventViewModel.loadEvents("Food")
    }

    // Loads the selected fragment into the fragment container
    private fun loadFragment(fragment: Fragment) {
        supportFragmentManager.beginTransaction()
            .replace(R.id.fragment_container, fragment)
            .commit()
    }

    // Updates the appearance of tabs (changes icon and text color)
    private fun updateTabAppearance(tab: TabLayout.Tab?, isSelected: Boolean) {
        tab?.customView?.let {
            val tabText = it.findViewById<TextView>(R.id.tab_text)
            val tabIcon = it.findViewById<ImageView>(R.id.tab_icon)

            val textColor = if (isSelected) R.color.bright else R.color.grey
            val iconColor = if (isSelected) R.color.bright else R.color.black

            tabText.setTextColor(ContextCompat.getColor(this, textColor))
            tabIcon.imageTintList = ColorStateList.valueOf(ContextCompat.getColor(this, iconColor))
        }
    }
}
