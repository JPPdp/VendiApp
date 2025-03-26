package com.example.vendiapp.view.main.profile

import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import com.example.vendiapp.R

class ProfileFragment : Fragment() {

    private lateinit var tvProfileName: TextView
    private lateinit var tvProfileEmail: TextView
    private lateinit var tvId: TextView

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_profile, container, false)

        // ✅ Initialize views
        initViews(view)

        // ✅ Load profile data locally
        loadUserProfile()

        // ✅ Navigate to Account Info Fragment
        val llRedirectToAccountInfo = view.findViewById<LinearLayout>(R.id.llRedirectToAccountInfo)
        llRedirectToAccountInfo.setOnClickListener {
            val fragment = AccountInfoFragment()
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, fragment)
                .addToBackStack(null)
                .commit()
        }

        // ✅ Navigate to Change Password Fragment
        val llChangePasswordNext = view.findViewById<LinearLayout>(R.id.llChangePasswordNext)
        llChangePasswordNext.setOnClickListener {
            val fragment = ChangePasswordFragment()
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, fragment)
                .addToBackStack(null)
                .commit()
        }

        return view
    }

    // ✅ Initialize views
    private fun initViews(view: View) {
        tvProfileName = view.findViewById(R.id.tvProfileName)
        tvProfileEmail = view.findViewById(R.id.tvProfileEmail)
        tvId = view.findViewById(R.id.tvId)
    }

    // ✅ Load profile data locally from SharedPreferences
    private fun loadUserProfile() {
        val userId = getUserIdFromPrefs()
        val userEmail = getUserEmailFromPrefs()
        val userName = getUserNameFromPrefs()

        if (userId.isNullOrEmpty() || userEmail.isNullOrEmpty()) {
            showToast("Error: User not logged in.")
            Log.e("ProfileFragment", "Error: userId or email is null or empty.")
            return
        }

        // ✅ Set profile data locally
        tvProfileName.text = userName ?: "N/A"
        tvProfileEmail.text = userEmail
        tvId.text = userId
        // 🔥 Log profile info
        Log.d("ProfileFragment", "Profile loaded locally. Name: $userName, Email: $userEmail")
    }

    // ✅ Get user ID from SharedPreferences
    private fun getUserIdFromPrefs(): String? {
        val sharedPreferences =
            requireActivity().getSharedPreferences("VendiAppPrefs", android.content.Context.MODE_PRIVATE)
        val userId = sharedPreferences.getString("userId", null)

        // 🔥 Log userId retrieval
        Log.d("ProfileFragment", "Retrieved userId: $userId")

        return userId
    }

    // ✅ Get user email from SharedPreferences
    private fun getUserEmailFromPrefs(): String? {
        val sharedPreferences =
            requireActivity().getSharedPreferences("VendiAppPrefs", android.content.Context.MODE_PRIVATE)
        val email = sharedPreferences.getString("email", null)

        // 🔥 Log email retrieval
        Log.d("ProfileFragment", "Retrieved email: $email")

        return email
    }

    // ✅ Get user name from SharedPreferences
    private fun getUserNameFromPrefs(): String? {
        val sharedPreferences =
            requireActivity().getSharedPreferences("VendiAppPrefs", android.content.Context.MODE_PRIVATE)
        val name = sharedPreferences.getString("name", null)

        // 🔥 Log name retrieval
        Log.d("ProfileFragment", "Retrieved name: $name")

        return name
    }

    // ✅ Show toast message
    private fun showToast(message: String) {
        Toast.makeText(requireContext(), message, Toast.LENGTH_SHORT).show()
    }
}
