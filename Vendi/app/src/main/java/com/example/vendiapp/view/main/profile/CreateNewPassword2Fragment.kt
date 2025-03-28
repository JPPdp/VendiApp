package com.example.vendiapp.view.main.profile

import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.View
import android.widget.Button
import android.widget.LinearLayout
import com.example.vendiapp.R

class CreateNewPassword2Fragment : Fragment(R.layout.fragment_create_new_password2) {

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val btnCreateNewPassword: Button = view.findViewById(R.id.btnCreateNewPassword)
        val llBack: LinearLayout = view.findViewById(R.id.llBack)

        // Navigate to LoginFragment when clicking "Create New Password"
        btnCreateNewPassword.setOnClickListener {
            val fragment = ProfileFragment() // Ensure LoginFragment is correctly imported
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, fragment) // Ensure fgtContainer is valid
                .addToBackStack(null)
                .commit()
        }

        // Handle Back Button
        llBack.setOnClickListener {
            parentFragmentManager.popBackStack() // Go back to the previous fragment
        }
    }
}
