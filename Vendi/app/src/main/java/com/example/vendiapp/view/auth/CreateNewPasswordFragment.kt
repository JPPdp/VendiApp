package com.example.vendiapp.view.auth

import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.LinearLayout
import com.example.vendiapp.R

class CreateNewPasswordFragment : Fragment() {

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        // Inflate the layout for this fragment
        val view = inflater.inflate(R.layout.fragment_create_new_password, container, false)

        val btnCreateNewPassword: Button = view.findViewById(R.id.btnCreateNewPassword)

        btnCreateNewPassword.setOnClickListener {
            val fragment = LogInFragment()
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, fragment)
                .addToBackStack(null)
                .commit()
        }

        val llBack = view.findViewById<LinearLayout>(R.id.llBack)
        llBack.setOnClickListener {
            parentFragmentManager.popBackStack() // Go back to the previous fragment
        }

        return view

    }

}