package com.example.vendiapp.view.main.profile

import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.View
import android.widget.Button
import android.widget.LinearLayout
import com.example.vendiapp.R
import com.example.vendiapp.view.main.profile.CreateNewPassword2Fragment

class ChangePasswordFragment : Fragment(R.layout.fragment_change_password) {

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val btnSubmit: Button = view.findViewById(R.id.btnSubmit)
        val llBack: LinearLayout = view.findViewById(R.id.llBack)

        // Handle submit button - Navigate to CreateNewPassword2Fragment
        btnSubmit.setOnClickListener {
            val fragment = CreateNewPassword2Fragment() // Ensure this class exists
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, fragment) // Ensure fgtContainer is valid
                .addToBackStack(null) // Allows user to navigate back
                .commit()
        }

        // Handle back button
        llBack.setOnClickListener {
            parentFragmentManager.popBackStack() // Go back to the previous fragment
        }
    }
}
