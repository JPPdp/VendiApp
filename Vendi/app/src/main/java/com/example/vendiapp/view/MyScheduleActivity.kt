package com.example.vendiapp.view

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.fragment.app.FragmentManager
import com.example.vendiapp.R

class MyScheduleActivity : AppCompatActivity() {

    private lateinit var fragmentManager: FragmentManager

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_schedule)

        supportFragmentManager.beginTransaction().replace(R.id.fgtContainer, MyScheduleFragment()).commit()


    }
}