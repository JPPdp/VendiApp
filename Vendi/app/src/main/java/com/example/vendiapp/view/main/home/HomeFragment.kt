package com.example.vendiapp.view.main.home

import android.content.Intent
import android.content.res.ColorStateList
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.ImageButton
import android.widget.ImageView
import android.widget.TextView
import androidx.core.content.ContextCompat
import androidx.fragment.app.Fragment
import androidx.fragment.app.activityViewModels
import androidx.fragment.app.commit
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import androidx.viewpager2.widget.ViewPager2
import com.example.vendiapp.R
import com.example.vendiapp.adapter.EventAdapter
import com.example.vendiapp.adapter.ViewPagerAdapter
import com.example.vendiapp.model.EventModel
import com.example.vendiapp.view.main.MessagesActivity
import com.example.vendiapp.viewmodel.EventViewModel
import com.google.android.material.tabs.TabLayout
import com.google.android.material.tabs.TabLayoutMediator

class HomeFragment : Fragment() {

    private lateinit var featuredAdapter: EventAdapter
    private val eventViewModel: EventViewModel by activityViewModels() // Shared ViewModel

    private var lastLoadedCategory: String = "Food" // Default category

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val view = inflater.inflate(R.layout.fragment_home, container, false)

        val tabLayout: TabLayout = view.findViewById(R.id.tabLayout)
        val viewPager: ViewPager2 = view.findViewById(R.id.viewPager)
        val featuredRecyclerView: RecyclerView = view.findViewById(R.id.rvFeaturedEvents)
        val tvTab: TextView = view.findViewById(R.id.tvTab)

        //Button Import & Function - Message
        val btnViewMessages : ImageButton = view.findViewById(R.id.imageButton)
        btnViewMessages.setOnClickListener {
            val intent = Intent(requireContext(), MessagesActivity::class.java)
            startActivity(intent)
        }

        setupFeaturedEventsRecyclerView(featuredRecyclerView)
        setupTabLayout(tabLayout, viewPager, tvTab)

        eventViewModel.loadEventsIfNeeded(lastLoadedCategory)

        return view
    }

    private fun setupFeaturedEventsRecyclerView(recyclerView: RecyclerView) {
        recyclerView.layoutManager = LinearLayoutManager(requireContext(), LinearLayoutManager.HORIZONTAL, false)
        featuredAdapter = EventAdapter(emptyList()) { event ->
            openEventDetails(event) // Use fragment instead of activity
        }
        recyclerView.adapter = featuredAdapter

        eventViewModel.featuredEvents.observe(viewLifecycleOwner) { featuredList ->
            featuredAdapter.updateEvents(featuredList)
        }
    }


    private fun setupTabLayout(tabLayout: TabLayout, viewPager: ViewPager2, tvTab: TextView) {
        val adapter = ViewPagerAdapter(requireActivity())
        viewPager.adapter = adapter
        viewPager.isUserInputEnabled = false

        val tabIcons = listOf(
            R.drawable.icon_noodle_white,
            R.drawable.icon_drink_black,
            R.drawable.icon_chair_grey
        )
        val tabTexts = listOf("Food", "Beverages", "Entertainment")

        TabLayoutMediator(tabLayout, viewPager) { tab, position ->
            val tabView = layoutInflater.inflate(R.layout.custom_tab, tabLayout, false)
            tabView.findViewById<ImageView>(R.id.tab_icon).setImageResource(tabIcons[position])
            tabView.findViewById<TextView>(R.id.tab_text).text = tabTexts[position]
            tab.customView = tabView
        }.attach()

        // Set default selected tab appearance
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

    private fun updateTabAppearance(tab: TabLayout.Tab?, isSelected: Boolean) {
        tab?.customView?.let {
            val tabText = it.findViewById<TextView>(R.id.tab_text)
            val tabIcon = it.findViewById<ImageView>(R.id.tab_icon)

            val textColor = if (isSelected) R.color.bright else R.color.grey
            val iconColor = if (isSelected) R.color.bright else R.color.black

            tabText.setTextColor(ContextCompat.getColor(requireContext(), textColor))
            tabIcon.imageTintList = ColorStateList.valueOf(ContextCompat.getColor(requireContext(), iconColor))
        }
    }

    // Open EventDetailsFragment instead of Activity
    private fun openEventDetails(event: EventModel) {
        parentFragmentManager.commit {
            replace(R.id.fgtContainer, EventDetailsFragment.newInstance(event))
            addToBackStack(null) // Enables back navigation
        }
    }
}