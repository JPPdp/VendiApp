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
import com.example.vendiapp.api.ApiUtils
import com.example.vendiapp.model.UserProfileResponse
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

        // ✅ Load profile data from DB using userId
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

    // ✅ Load profile data using userId from DB
    private fun loadUserProfile() {
        val userId = getUserIdFromPrefs()
        val userEmail = getUserEmailFromPrefs()

        if (userId.isNullOrEmpty() || userEmail.isNullOrEmpty()) {
            showToast("Error: User not logged in.")
            Log.e("ProfileFragment", "Error: userId or email is null or empty.")
            return
        }

        // ✅ Fetch full_name using userId from DB
        fetchUserFullName(userId)

        // ✅ Set email and ID locally
        tvProfileEmail.text = userEmail
        tvId.text = "User ID: $userId"

        // 🔥 Log profile info
        Log.d("ProfileFragment", "Profile loaded locally. Email: $userEmail, ID: $userId")
    }

    // ✅ Fetch full_name from DB using userId
    private fun fetchUserFullName(userId: String) {
        ApiUtils.apiService.getUserProfile(userId).enqueue(object : Callback<UserProfileResponse> {
            override fun onResponse(
                call: Call<UserProfileResponse>,
                response: Response<UserProfileResponse>
            ) {
                if (response.isSuccessful && response.body() != null) {
                    val userProfile = response.body()!!
                    tvProfileName.text = userProfile.full_name ?: "N/A"
                    Log.d("ProfileFragment", "Fetched Name: ${userProfile.full_name}")
                } else {
                    tvProfileName.text = "N/A"
                    showToast("Failed to load profile. Please try again.")
                    Log.e("ProfileFragment", "Error fetching full_name from DB.")
                }
            }

            override fun onFailure(call: Call<UserProfileResponse>, t: Throwable) {
                tvProfileName.text = "N/A"
                showToast("Failed to load profile. Please check your connection.")
                Log.e("ProfileFragment", "Network error: ${t.message}")
            }
        })
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

    // ✅ Show toast message
    private fun showToast(message: String) {
        Toast.makeText(requireContext(), message, Toast.LENGTH_SHORT).show()
    }
}
