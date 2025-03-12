package com.example.vendiapp

import android.app.ActivityOptions
import android.content.Context
import android.content.Intent
import android.content.res.ColorStateList
import android.util.Log
import android.widget.ImageView
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import androidx.viewpager2.widget.ViewPager2
import com.google.android.material.bottomnavigation.BottomNavigationView
import com.google.android.material.tabs.TabLayout
import com.google.android.material.tabs.TabLayoutMediator

// Extension to load fragments
fun AppCompatActivity.loadFragment(fragment: Fragment) {
    supportFragmentManager.beginTransaction()
        .replace(R.id.fragment_container, fragment)
        .commit()
}

// Extension to update tab appearance
fun AppCompatActivity.updateTabAppearance(tab: TabLayout.Tab?, isSelected: Boolean) {
    tab?.customView?.let {
        val tabText = it.findViewById<TextView>(R.id.tab_text)
        val tabIcon = it.findViewById<ImageView>(R.id.tab_icon)

        val textColor = if (isSelected) R.color.bright else R.color.grey
        val iconColor = if (isSelected) R.color.bright else R.color.black

        tabText.setTextColor(ContextCompat.getColor(this, textColor))
        tabIcon.imageTintList = ColorStateList.valueOf(ContextCompat.getColor(this, iconColor))
    }
}

// Extension to setup the featured RecyclerView
fun AppCompatActivity.setupFeaturedRecyclerView(
    recyclerView: RecyclerView,
    adapter: EventAdapter
) {
    recyclerView.layoutManager = LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false)
    recyclerView.adapter = adapter
}

// Extension to observe ViewModel data
fun AppCompatActivity.observeEventViewModel(
    viewModel: EventViewModel,
    adapter: EventAdapter
) {
    viewModel.events.observe(this) { newList ->
        adapter.updateData(newList)
    }
}

// Extension to setup TabLayout with ViewPager2
fun AppCompatActivity.setupTabLayoutWithViewPager(
    tabLayout: TabLayout,
    viewPager: ViewPager2,
    tabIcons: List<Int>,
    tabTexts: List<String>
) {
    TabLayoutMediator(tabLayout, viewPager) { tab, position ->
        val tabView = layoutInflater.inflate(R.layout.custom_tab, null)
        val tabIcon = tabView.findViewById<ImageView>(R.id.tab_icon)
        val tabText = tabView.findViewById<TextView>(R.id.tab_text)

        tabIcon.setImageResource(tabIcons[position])
        tabText.text = tabTexts[position]
        tab.customView = tabView
    }.attach()
}

// Extension to setup BottomNavigation
fun AppCompatActivity.setupBottomNavigation(
    bottomNavigationView: BottomNavigationView
) {
    bottomNavigationView.setOnItemSelectedListener { item ->
        when (item.itemId) {
            R.id.nav_home -> {
                val intent = Intent(this, MainActivity::class.java)
                finish()
                val options = ActivityOptions.makeCustomAnimation(this, 0, 0).toBundle()
                startActivity(intent, options)
            }
            R.id.nav_schedule -> loadFragment(ScheduleFragment())
            R.id.nav_profile -> loadFragment(ProfileFragment())
        }
        true
    }
}
// Extension to convert Event to Intent for StallDetailsActivity
fun Event.toIntent(context: Context): Intent {
    return Intent(context, StallDetailsActivity::class.java).apply {
        putExtra("eventTitle", title)
        putExtra("eventSubTitle", subTitle)
        putExtra("eventDescription", description)
        putExtra("eventImage", imageRes)
        putExtra("eventLocation", location)
        putExtra("eventRating", rating.toDouble())
        putExtra("eventPrice", price)
    }
}

// Extension to handle tab selection
fun AppCompatActivity.handleTabSelection(
    tabLayout: TabLayout,
    viewPager: ViewPager2,
    tvTab: TextView,
    viewModel: EventViewModel
) {
    tabLayout.addOnTabSelectedListener(object : TabLayout.OnTabSelectedListener {
        override fun onTabSelected(tab: TabLayout.Tab?) {
            updateTabAppearance(tab, isSelected = true)
            viewPager.setCurrentItem(tab?.position ?: 0, false)

            val category = when (tab?.position) {
                0 -> "Food"
                1 -> "Beverages"
                2 -> "Entertainment"
                else -> "Food"
            }
            tvTab.text = category
            viewModel.loadEvents(category)
        }

        override fun onTabUnselected(tab: TabLayout.Tab?) {
            updateTabAppearance(tab, isSelected = false)
        }

        override fun onTabReselected(tab: TabLayout.Tab?) {}
    })
}
