package com.example.vendiapp.view.main.profile

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.LinearLayout
import androidx.fragment.app.Fragment
import com.example.vendiapp.R

class ProfileFragment : Fragment() {

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_profile, container, false)

        // Navigate to Account Info Fragment
        val llRedirectToAccountInfo = view.findViewById<LinearLayout>(R.id.llRedirectToAccountInfo)
        llRedirectToAccountInfo.setOnClickListener {
            val fragment = AccountInfoFragment()
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, fragment)
                .addToBackStack(null) // Allows user to navigate back
                .commit()
        }

        val llChangePasswordNext = view.findViewById<LinearLayout>(R.id.llChangePasswordNext)
        llChangePasswordNext.setOnClickListener {
            val fragment = ChangePasswordFragment()
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, fragment)
                .addToBackStack(null) // Allows user to navigate back
                .commit()
        }

        return view

    }

}
