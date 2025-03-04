package com.example.vendi
import android.view.View
import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import com.example.vendi.fragments.HomeFragment
import com.example.vendi.fragments.ProfileFragment
import com.example.vendi.fragments.ScheduleFragment
import com.google.android.material.bottomnavigation.BottomNavigationView
object MainActivityHelper {
    /**
     * Initialize fragments for navigation.
     */
    fun initializeFragments(
        homeFragment: HomeFragment,
        scheduleFragment: ScheduleFragment,
        profileFragment: ProfileFragment,
        activity: FragmentActivity
    ) {
        // You can optionally add any additional logic here for initializing fragments
    }

    /**
     * Set up the bottom navigation item selection listener.
     */
    fun setupBottomNavigation(
        bottomNavigationView: BottomNavigationView,
        homeFragment: HomeFragment,
        scheduleFragment: ScheduleFragment,
        profileFragment: ProfileFragment,
        activity: FragmentActivity
    ) {
        bottomNavigationView.setOnItemSelectedListener { item ->
            when (item.itemId) {
                R.id.menu_Home -> makeCurrentFragment(homeFragment, activity)
                R.id.menu_Schedule -> makeCurrentFragment(scheduleFragment, activity)
                R.id.menu_Profile -> makeCurrentFragment(profileFragment, activity)
                else -> false
            }
            true
        }
    }

    /**
     * Apply window insets to ensure content is appropriately adjusted for system bars.
     */
    fun setupWindowInsets(view: View) {
        ViewCompat.setOnApplyWindowInsetsListener(view) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom)
            insets
        }
    }

    /**
     * Replace the current fragment displayed in the fragment container.
     * @param fragment The fragment to be displayed.
     */
    private fun makeCurrentFragment(fragment: Fragment, activity: FragmentActivity) =
        activity.supportFragmentManager.beginTransaction().apply {
            replace(R.id.fl_wrapper, fragment)
            commit()
        }
}