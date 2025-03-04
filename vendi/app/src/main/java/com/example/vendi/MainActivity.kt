package com.example.vendi

import android.os.Bundle
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.fragment.app.Fragment
import com.example.vendi.fragments.HomeFragment
import com.example.vendi.fragments.ProfileFragment
import com.example.vendi.fragments.ScheduleFragment
import com.google.android.material.bottomnavigation.BottomNavigationView

class MainActivity : AppCompatActivity() {

    // Fragment instances initialized lazily to avoid nullability issues and unnecessary initializations
    private val homeFragment by lazy { HomeFragment() }
    private val scheduleFragment by lazy { ScheduleFragment() }
    private val profileFragment by lazy { ProfileFragment() }
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_main)
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main)) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom)
            insets
        }

        MainActivityHelper.initializeFragments(homeFragment, scheduleFragment, profileFragment, this)

        // Set up the bottom navigation and its item selection listener
        val bottomNavigationView = findViewById<BottomNavigationView>(R.id.bottom_nav)
        MainActivityHelper.setupBottomNavigation(bottomNavigationView, homeFragment, scheduleFragment, profileFragment, this)

        // Apply window insets listener for adjusting the view layout for edge-to-edge display
        MainActivityHelper.setupWindowInsets(findViewById(R.id.main))
    }
}