package com.example.vendiapp.view.main.profile

import android.content.Context
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
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

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

        // ✅ Load profile data from DB using clientId
        loadUserProfile()

        // ✅ Navigate to Account Info Fragment
        val llRedirectToAccountInfo = view.findViewById<LinearLayout>(R.id.llRedirectToAccountInfo)
        llRedirectToAccountInfo.setOnClickListener {
            val fragment = AccountInfoFragment()
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, fragment)
                .addToBackStack(null)
                .commitAllowingStateLoss()
        }

        // ✅ Navigate to Change Password Fragment
        val llChangePasswordNext = view.findViewById<LinearLayout>(R.id.llChangePasswordNext)
        llChangePasswordNext.setOnClickListener {
            val fragment = ChangePasswordFragment()
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, fragment)
                .addToBackStack(null)
                .commitAllowingStateLoss()
        }

        return view
    }

    // ✅ Initialize views
    private fun initViews(view: View) {
        tvProfileName = view.findViewById(R.id.tvProfileName)
        tvProfileEmail = view.findViewById(R.id.tvProfileEmail)
        tvId = view.findViewById(R.id.tvId)
    }

    // ✅ Load profile data using clientId from DB
    private fun loadUserProfile() {
        val clientId = getClientIdFromPrefs()
        val userEmail = getUserEmailFromPrefs()
        val userName = getUserNameFromPrefs()

        if (clientId.isNullOrEmpty() || userEmail.isNullOrEmpty()) {
            showToast("Error: User not logged in.")
            Log.e("ProfileFragment", "Error: clientId or email is null or empty.")
            return
        }


        // ✅ Set email and ID locally

        tvProfileEmail.text = userEmail
        tvId.text = clientId
        tvProfileName.text = userName


        // 🔥 Log profile info
        Log.d("ProfileFragment", "Profile loaded locally. Email: $userEmail, ID: $clientId")
    }


    // ✅ Get user ID from SharedPreferences
    private fun getClientIdFromPrefs(): String? {
        val sharedPreferences =
            requireActivity().getSharedPreferences("VendiAppPrefs", android.content.Context.MODE_PRIVATE)
        val clientId = sharedPreferences.getString("clientId", null)

        // 🔥 Log clientId retrieval
        Log.d("ProfileFragment", "Retrieved clientId: $clientId")

        return clientId
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

    // ✅ Get user email from SharedPreferences
    private fun getUserNameFromPrefs(): String? {
        val sharedPreferences =
            requireActivity().getSharedPreferences("VendiAppPrefs", android.content.Context.MODE_PRIVATE)
        val name = sharedPreferences.getString("name", null)

        // 🔥 Log email retrieval
        Log.d("ProfileFragment", "Retrieved : $name")

        return name
    }


    // ✅ Show toast message
    private fun showToast(message: String) {
        Toast.makeText(requireContext(), message, Toast.LENGTH_SHORT).show()
    }
}
