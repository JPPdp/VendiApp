package com.example.vendiapp

import androidx.appcompat.app.AppCompatActivity
import androidx.fragment.app.Fragment
import androidx.viewpager2.adapter.FragmentStateAdapter

class ViewPagerAdapter(activity: AppCompatActivity) : FragmentStateAdapter(activity) {

    override fun getItemCount(): Int {
        return 3 // We have 3 tabs (Food, Beverages, Entertainment)
    }

    override fun createFragment(position: Int): Fragment {
        return when (position) {
            0 -> FoodFragment()         // First Tab (Food)
            1 -> BeveragesFragment()    // Second Tab (Beverages)
            2 -> EntertainmentFragment()// Third Tab (Entertainment)
            else -> FoodFragment()
        }
    }
}