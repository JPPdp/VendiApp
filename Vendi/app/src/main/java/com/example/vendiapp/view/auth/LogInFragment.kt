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
import androidx.fragment.app.viewModels
import com.example.vendiapp.R
import com.example.vendiapp.api.ApiUtils
import com.example.vendiapp.view.main.MainActivity
import com.example.vendiapp.viewmodel.LogInViewModel

class LogInFragment : Fragment() {

    private val loginViewModel: LogInViewModel by viewModels()
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
        etEmail = view.findViewById(R.id.etEmail)
        etPassword = view.findViewById(R.id.etPassword)
        btnSignIn = view.findViewById(R.id.btnSignIn)
        btnSignUp = view.findViewById(R.id.btnSignUp)
        tvForgotPassword = view.findViewById(R.id.tvForgotPassword)

        // ✅ Initialize SharedPreferences
        sharedPreferences =
            requireActivity().getSharedPreferences("VendiAppPrefs", Context.MODE_PRIVATE)

        // ✅ Check if the user is already logged in
        if (isUserLoggedIn()) {
            navigateToMainActivity()
        }

        // ✅ Sign-in button click
        btnSignIn.setOnClickListener {
            val email = etEmail.text.toString().trim()
            val password = etPassword.text.toString().trim()

            if (email.isEmpty() || password.isEmpty()) {
                Toast.makeText(requireContext(), "Please fill in all fields!", Toast.LENGTH_SHORT)
                    .show()
            } else {
                loginUser(email, password)
            }
        }

        // ✅ Sign-up button click (Navigate to RegistrationFragment1)
        btnSignUp.setOnClickListener {
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, RegistrationFragment1())
                .addToBackStack(null)
                .commit()
        }

        // ✅ Forgot password button click (Navigate to PasswordRecoveryFragment)
        tvForgotPassword.setOnClickListener {
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, PasswordRecoveryFragment())
                .addToBackStack(null)
                .commit()
        }

        return view
    }

    // ✅ Login user and store session in SharedPreferences
    private fun loginUser(email: String, password: String) {
        ApiUtils.loginUserToDB(email, password) { success, userId, message ->
            requireActivity().runOnUiThread {
                if (success && !userId.isNullOrEmpty()) {
                    Log.d("LogInFragment", "User ID received: $userId")
                    saveUserSession(userId) // Save session after successful login
                    Toast.makeText(requireContext(), "Login successful!", Toast.LENGTH_SHORT)
                        .show()
                    navigateToMainActivity()
                } else {
                    Log.e("LogInFragment", "Login failed: $message")
                    Toast.makeText(requireContext(), message, Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    // ✅ Save user session
    private fun saveUserSession(userId: String) {
        val editor = sharedPreferences.edit()
        editor.putBoolean("isLoggedIn", true)
        editor.putString("userId", userId)
        editor.apply()
    }

    // ✅ Check if user is logged in
    private fun isUserLoggedIn(): Boolean {
        return sharedPreferences.getBoolean("isLoggedIn", false)
    }

    // ✅ Navigate to MainActivity after login
    private fun navigateToMainActivity() {
        val intent = Intent(requireActivity(), MainActivity::class.java)
        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        startActivity(intent)
        requireActivity().finish()
    }
}
