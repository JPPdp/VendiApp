package com.example.vendiapp.adapter

import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentActivity
import androidx.viewpager2.adapter.FragmentStateAdapter
import com.example.vendiapp.view.main.home.EventListFragment

class ViewPagerAdapter(activity: FragmentActivity) : FragmentStateAdapter(activity) {
    override fun getItemCount(): Int = 3

    override fun createFragment(position: Int): Fragment {
        val category = when (position) {
            0 -> "Food"  // Use consistent casing
            1 -> "Beverages"
            2 -> "Entertainment"
            else -> "All"
        }
        return EventListFragment.newInstance(category)
    }
}