package com.example.vendiapp.view.auth

import android.content.Context
import android.content.Intent
import android.content.SharedPreferences
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.EditText
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import com.example.vendiapp.R
import com.example.vendiapp.api.ApiUtils
import com.example.vendiapp.view.main.MainActivity

class LogInFragment : Fragment() {

    private lateinit var etEmail: EditText
    private lateinit var etPassword: EditText
    private lateinit var btnSignIn: Button
    private lateinit var btnSignUp: Button
    private lateinit var tvForgotPassword: TextView
    private lateinit var sharedPreferences: SharedPreferences

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_log_in, container, false)

        // ✅ Initialize views
        initViews(view)

        // ✅ Initialize SharedPreferences
        sharedPreferences = requireActivity().getSharedPreferences("VendiAppPrefs", Context.MODE_PRIVATE)

        // ✅ Check if user is already logged in
        if (isUserLoggedIn()) {
            Log.d("LogInFragment", "User already logged in. Redirecting to MainActivity.")
            navigateToMainActivity()
        }

        // ✅ Set click listeners
        setClickListeners()

        return view
    }

    // ✅ Initialize views
    private fun initViews(view: View) {
        etEmail = view.findViewById(R.id.etEmail)
        etPassword = view.findViewById(R.id.etPassword)
        btnSignIn = view.findViewById(R.id.btnSignIn)
        btnSignUp = view.findViewById(R.id.btnSignUp)
        tvForgotPassword = view.findViewById(R.id.tvForgotPassword)
    }

    // ✅ Set button click listeners
    private fun setClickListeners() {
        btnSignIn.setOnClickListener {
            val email = etEmail.text.toString().trim()
            val password = etPassword.text.toString().trim()

            if (validateInputs(email, password)) {
                loginUser(email, password)
            }
        }

        btnSignUp.setOnClickListener {
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, RegistrationFragment1())
                .addToBackStack(null)
                .commit()
        }

        tvForgotPassword.setOnClickListener {
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, PasswordRecoveryFragment())
                .addToBackStack(null)
                .commit()
        }
    }

    // ✅ Validate user inputs
    private fun validateInputs(email: String, password: String): Boolean {
        return when {
            email.isEmpty() -> {
                showToast("Please enter your email.")
                false
            }
            password.isEmpty() -> {
                showToast("Please enter your password.")
                false
            }
            else -> true
        }
    }

    // ✅ Login user using API
    private fun loginUser(email: String, password: String) {
        ApiUtils.loginUserToDB(email, password) { success, userId, message ->
            requireActivity().runOnUiThread {
                if (success && !userId.isNullOrEmpty()) {
                    saveUserSession(userId, email, password)
                    navigateToMainActivity()
                } else {
                    showToast(message)
                }
            }
        }
    }

    // ✅ Save user session with email and password
    private fun saveUserSession(userId: String, email: String, password: String) {
        sharedPreferences.edit().apply {
            putBoolean("isLoggedIn", true)
            putString("userId", userId)
            putString("email", email)
            putString("password", password)
            apply()
        }
    }

    // ✅ Check if user is logged in
    private fun isUserLoggedIn(): Boolean {
        return sharedPreferences.getBoolean("isLoggedIn", false)
    }

    // ✅ Navigate to MainActivity after login
    private fun navigateToMainActivity() {
        val intent = Intent(requireActivity(), MainActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        }
        startActivity(intent)
        requireActivity().finish()
    }

    // ✅ Show toast message
    private fun showToast(message: String) {
        Toast.makeText(requireContext(), message, Toast.LENGTH_SHORT).show()
    }
}
