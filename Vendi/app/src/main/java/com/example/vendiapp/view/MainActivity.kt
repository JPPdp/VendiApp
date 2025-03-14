package com.example.vendiapp.view

import android.app.ActivityOptions
import android.content.Intent
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
import com.example.vendiapp.adapter.EventAdapter
import com.example.vendiapp.viewmodel.EventViewModel
import com.example.vendiapp.ProfileFragment
import com.example.vendiapp.R
import com.example.vendiapp.ScheduleFragment
import com.example.vendiapp.adapter.ViewPagerAdapter
import com.google.android.material.bottomnavigation.BottomNavigationView
import com.google.android.material.tabs.TabLayout
import com.google.android.material.tabs.TabLayoutMediator

class MainActivity : AppCompatActivity() {

    private lateinit var featuredAdapter: EventAdapter
    private val eventViewModel: EventViewModel by viewModels()
    private var lastLoadedCategory: String = "Food" // Set default value to avoid null issues

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        val tabLayout: TabLayout = findViewById(R.id.tabLayout)
        val viewPager: ViewPager2 = findViewById(R.id.viewPager)
        val featuredRecyclerView: RecyclerView = findViewById(R.id.rvFeaturedEvents)
        val tvTab: TextView = findViewById(R.id.tvTab)

        setupFeaturedEventsRecyclerView(featuredRecyclerView)
        setupTabLayout(tabLayout, viewPager, tvTab)
        setupBottomNavigation()

        // Load default category
        eventViewModel.loadEventsIfNeeded(lastLoadedCategory)
    }

    private fun setupFeaturedEventsRecyclerView(recyclerView: RecyclerView) {
        recyclerView.layoutManager = LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false)
        featuredAdapter = EventAdapter(emptyList()) { event ->
            val intent = Intent(this, EventDetailsActivity::class.java).apply {
                putExtra("eventTitle", event.title)
                putExtra("eventSubTitle", event.subTitle)
                putExtra("eventDescription", event.description)
                putExtra("eventImage", event.imageRes)
                putExtra("eventLocation", event.location)
                putExtra("eventRating", event.rating)// Assuming rating is Int or Float
                putExtra("eventPrice", event.price)
            }
            startActivity(intent)
        }
        recyclerView.adapter = featuredAdapter

        eventViewModel.featuredEvents.observe(this) { featuredList ->
            featuredAdapter.updateEvents(featuredList)
        }
    }

    private fun setupTabLayout(tabLayout: TabLayout, viewPager: ViewPager2, tvTab: TextView) {
        val adapter = ViewPagerAdapter(this)
        viewPager.adapter = adapter
        viewPager.isUserInputEnabled = false

        val tabIcons = listOf(R.drawable.icon_noodle_white, R.drawable.icon_drink_black, R.drawable.icon_chair_grey)
        val tabTexts = listOf("Food", "Beverages", "Entertainment")

        TabLayoutMediator(tabLayout, viewPager) { tab, position ->
            val tabView = layoutInflater.inflate(R.layout.custom_tab, tabLayout, false)
            tabView.findViewById<ImageView>(R.id.tab_icon).setImageResource(tabIcons[position])
            tabView.findViewById<TextView>(R.id.tab_text).text = tabTexts[position]
            tab.customView = tabView
        }.attach()

        updateTabAppearance(tabLayout.getTabAt(0), isSelected = true)
        tvTab.text = ""

        tabLayout.addOnTabSelectedListener(object : TabLayout.OnTabSelectedListener {
            override fun onTabSelected(tab: TabLayout.Tab?) {
                updateTabAppearance(tab, isSelected = true)
                val position = tab?.position ?: 0
                val category = tabTexts.getOrNull(position) ?: "Food"

                viewPager.setCurrentItem(position, false)
                tvTab.text = category

                // Fetch data only if category changed
                if (lastLoadedCategory != category) {
                    eventViewModel.loadEventsIfNeeded(category)
                    lastLoadedCategory = category
                }
            }

            override fun onTabUnselected(tab: TabLayout.Tab?) {
                updateTabAppearance(tab, isSelected = false)
            }

            override fun onTabReselected(tab: TabLayout.Tab?) {
                // Avoid unnecessary reloads on re-selection
            }
        })
    }

    private fun setupBottomNavigation() {
        val bottomNavigationView = findViewById<BottomNavigationView>(R.id.bottomNavigationView)
        bottomNavigationView.setOnItemSelectedListener { item ->
            when (item.itemId) {
                R.id.nav_home -> restartMainActivity()
                R.id.nav_schedule -> loadFragment(ScheduleFragment())
                R.id.nav_profile -> loadFragment(ProfileFragment())
            }
            true
        }
    }

    private fun restartMainActivity() {
        val intent = Intent(this, MainActivity::class.java)
        finish()
        val options = ActivityOptions.makeCustomAnimation(this, 0, 0).toBundle()
        startActivity(intent, options)
    }

    private fun loadFragment(fragment: Fragment) {
        supportFragmentManager.beginTransaction()
            .replace(R.id.fragment_container, fragment)
            .commit()
    }

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
