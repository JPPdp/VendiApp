package com.example.vendiapp.view.auth

import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.LinearLayout
import android.widget.TextView
import com.example.vendiapp.R


class RegistrationFragment2 : Fragment() {

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        // Inflate the layout for this fragment
        val view = inflater.inflate(R.layout.fragment_registration2, container, false)

        val btnNext: Button = view.findViewById(R.id.btnNext)

        btnNext.setOnClickListener {
            val fragment = RegistrationFragment3()
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, fragment)
                .addToBackStack(null) // Allows user to navigate back
                .commit()
        }

        val llBack = view.findViewById<LinearLayout>(R.id.llBack)
        llBack.setOnClickListener {
            parentFragmentManager.popBackStack() // Go back to the previous fragment
        }

        val tvSkip: TextView = view.findViewById(R.id.tvSkip)

        // Handle Skip button click
        tvSkip.setOnClickListener {
            val fragment = RegistrationFragment3() // Replace with your actual destination fragment

            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, fragment) // Replace with the container in your activity layout
                .addToBackStack(null) // Allows user to navigate back if needed
                .commit()
        }

        return view
    }

}