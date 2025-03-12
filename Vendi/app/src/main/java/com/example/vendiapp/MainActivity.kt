package com.example.vendiapp

import android.os.Bundle
import android.widget.TextView
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.RecyclerView
import androidx.viewpager2.widget.ViewPager2
import com.google.android.material.bottomnavigation.BottomNavigationView
import com.google.android.material.tabs.TabLayout

class MainActivity : AppCompatActivity() {

    private lateinit var featuredAdapter: EventAdapter
    private val eventViewModel: EventViewModel by viewModels()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        val tabLayout: TabLayout = findViewById(R.id.tabLayout)
        val viewPager: ViewPager2 = findViewById(R.id.viewPager)
        val featuredRecyclerView: RecyclerView = findViewById(R.id.rvFeaturedEvents)
        val tvTab: TextView = findViewById(R.id.tvTab)
        val bottomNavigationView: BottomNavigationView = findViewById(R.id.bottomNavigationView)

        featuredAdapter = EventAdapter(emptyList()) { event ->
            startActivity(event.toIntent(this))
        }

        setupFeaturedRecyclerView(featuredRecyclerView, featuredAdapter)
        observeEventViewModel(eventViewModel, featuredAdapter)

        val tabIcons = listOf(R.drawable.icon_noodle_white, R.drawable.icon_drink_black, R.drawable.icon_chair_grey)
        val tabTexts = listOf("Food", "Beverages", "Entertainment")

        viewPager.adapter = ViewPagerAdapter(this)
        viewPager.isUserInputEnabled = false
        viewPager.setCurrentItem(0, false)
        tabLayout.selectTab(tabLayout.getTabAt(0))

        setupTabLayoutWithViewPager(tabLayout, viewPager, tabIcons, tabTexts)
        updateTabAppearance(tabLayout.getTabAt(0), isSelected = true)
        tvTab.text = "Food"

        handleTabSelection(tabLayout, viewPager, tvTab, eventViewModel)
        setupBottomNavigation(bottomNavigationView)

        eventViewModel.loadEvents("Food")
    }
}