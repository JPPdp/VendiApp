package com.example.vendiapp.view.main.profile

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.LinearLayout
import android.widget.TextView
import androidx.appcompat.app.AlertDialog
import androidx.fragment.app.Fragment
import com.example.vendiapp.R
import com.example.vendiapp.model.ClientRequest
import com.example.vendiapp.view.auth.AuthActivity

class ProfileFragment : Fragment() {

    private lateinit var llLogOutIcon: LinearLayout
    private lateinit var tvProfileName: TextView
    private lateinit var tvProfileEmail: TextView
    private lateinit var tvId: TextView

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val view = inflater.inflate(R.layout.fragment_profile, container, false)
        initViews(view)
        setupLogoutButton()
        updateProfileData() // Load data immediately
        return view
    }

    override fun onResume() {
        super.onResume()
        updateProfileData() // Reload data when fragment resumes
    }

    private fun initViews(view: View) {
        tvProfileName = view.findViewById(R.id.tvProfileName)
        tvProfileEmail = view.findViewById(R.id.tvProfileEmail)
        tvId = view.findViewById(R.id.tvId)
        llLogOutIcon = view.findViewById(R.id.llLogOutIcon)
    }

    private fun setupLogoutButton() {
        llLogOutIcon.setOnClickListener { showLogoutDialog() }
    }

    private fun updateProfileData() {
        val userData = getUserDataFromPreferences()
        Log.d("ProfileFragment", "Retrieved data: $userData") // Debug log

        userData?.let {
            tvProfileName.text = it.name.ifEmpty { "No name available" }
            tvProfileEmail.text = it.email.ifEmpty { "No email available" }
            tvId.text = it.mobile_number.ifEmpty { "No mobile number available" }
        } ?: run {
            tvProfileName.text = "No name available"
            tvProfileEmail.text = "No email available"
            tvId.text = "No mobile number available"
        }
    }

    private fun showLogoutDialog() {
        AlertDialog.Builder(requireContext())
            .setTitle("Log Out")
            .setMessage("Are you sure you want to log out?")
            .setPositiveButton("Yes") { _, _ -> logoutUser() }
            .setNegativeButton("Cancel", null)
            .show()
    }

    private fun logoutUser() {
        val sharedPreferences = requireContext().getSharedPreferences("UserSession", Context.MODE_PRIVATE)
        sharedPreferences.edit().clear().apply()

        val intent = Intent(requireContext(), AuthActivity::class.java)
        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        startActivity(intent)
        requireActivity().finish()
    }

    private fun getUserDataFromPreferences(): ClientRequest? {
        val sharedPreferences = requireContext().getSharedPreferences("UserSession", Context.MODE_PRIVATE)
        val name = sharedPreferences.getString("name", "") ?: ""
        val email = sharedPreferences.getString("email", "") ?: ""
        val mobileNumber = sharedPreferences.getString("mobile_number", "") ?: ""

        return if (name.isNotEmpty() || email.isNotEmpty()) {
            ClientRequest(name, email, mobileNumber, "")
        } else {
            null
        }
    }
}