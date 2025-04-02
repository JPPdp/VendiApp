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
import com.example.vendiapp.api.RetrofitClient
import com.example.vendiapp.model.LoginRequest
import com.example.vendiapp.model.LoginResponse
import com.example.vendiapp.view.main.MainActivity
import com.google.gson.JsonSyntaxException
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.io.IOException

class LogInFragment : Fragment() {

    private lateinit var etEmail: EditText
    private lateinit var etPassword: EditText
    private lateinit var btnSignIn: Button
    private lateinit var btnSignUp: Button
    private lateinit var tvForgotPassword: TextView
    private lateinit var sharedPreferences: SharedPreferences

    override fun onAttach(context: Context) {
        super.onAttach(context)
        sharedPreferences = context.getSharedPreferences("UserPrefs", Context.MODE_PRIVATE)
    }

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_log_in, container, false)

        etEmail = view.findViewById(R.id.etEmail)
        etPassword = view.findViewById(R.id.etPassword)
        btnSignIn = view.findViewById(R.id.btnSignIn)
        btnSignUp = view.findViewById(R.id.btnSignUp)
        tvForgotPassword = view.findViewById(R.id.tvForgotPassword)

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

        return view
    }

    private fun validateInputs(email: String, password: String): Boolean {
        return when {
            email.isEmpty() -> {
                etEmail.error = "Please enter your email"
                false
            }
            !android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches() -> {
                etEmail.error = "Please enter a valid email"
                false
            }
            password.isEmpty() -> {
                etPassword.error = "Please enter your password"
                false
            }
            password.length < 6 -> {
                etPassword.error = "Password must be at least 6 characters"
                false
            }
            else -> true
        }
    }

    private fun loginUser(email: String, password: String) {
        val request = LoginRequest(email, password)

        RetrofitClient.instance.getClient(request).enqueue(object : Callback<LoginResponse> {
            override fun onResponse(call: Call<LoginResponse>, response: Response<LoginResponse>) {
                try {
                    if (response.isSuccessful) {
                        response.body()?.let { loginResponse ->
                            when (loginResponse.status) {
                                "success" -> {
                                    loginResponse.client_id?.let { clientId ->
                                        // Save ALL user data (add name + mobile_number if available)
                                        saveUserSession(
                                            clientId = clientId,
                                            email = email,
                                            name = loginResponse.name ?: "", // Get from API
                                            mobileNumber = loginResponse.mobile_number ?: "" // Get from API
                                        )
                                        navigateToMainActivity()
                                    } ?: showToast("Login successful but missing client ID")
                                }
                                // ... rest of your code
                            }
                        }
                    }
                } catch (e: Exception) {
                    Log.e("LOGIN_ERROR", "Response processing error", e)
                    showToast("Error processing login response")
                }
            }

            override fun onFailure(call: Call<LoginResponse>, t: Throwable) {
                Log.e("LOGIN_ERROR", "Login failed", t)
                showToast(
                    when (t) {
                        is IOException -> "Network error. Please check your connection"
                        is JsonSyntaxException -> "Server sent malformed response. Please try again."
                        else -> "Unexpected error: ${t.localizedMessage}"
                    }
                )
            }
        })
    }

    private fun handleErrorResponse(response: Response<LoginResponse>) {
        try {
            val errorBody = response.errorBody()?.string()
            Log.e("LOGIN_ERROR", "Status ${response.code()}: $errorBody")

            try {
                // Try to parse as standard error format
                val errorResponse = RetrofitClient.gson.fromJson(errorBody, LoginResponse::class.java)
                showToast(errorResponse.message ?: "Error ${response.code()}")
            } catch (e: JsonSyntaxException) {
                // Fallback to raw error message
                showToast(errorBody ?: "Error ${response.code()}")
            }
        } catch (e: Exception) {
            Log.e("LOGIN_ERROR", "Error processing error response", e)
            showToast("Error ${response.code()}")
        }
    }

    private fun saveUserSession(clientId: String, email: String, name: String, mobileNumber: String) {
        sharedPreferences.edit().apply {
            putBoolean("isLoggedIn", true)
            putString("clientId", clientId)
            putString("email", email)
            putString("name", name)               // Save name
            putString("mobile_number", mobileNumber) // Save mobile number
            apply()
        }
    }

    private fun navigateToMainActivity() {
        val intent = Intent(requireActivity(), MainActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        }
        startActivity(intent)
        requireActivity().finish()
    }

    private fun showToast(message: String) {
        Toast.makeText(requireContext(), message, Toast.LENGTH_SHORT).show()
    }

    private fun saveUserData(name: String, email: String, mobileNumber: String) {
        val sharedPreferences = requireContext().getSharedPreferences("UserSession", Context.MODE_PRIVATE)
        with(sharedPreferences.edit()) {
            putString("name", name)
            putString("email", email)
            putString("mobile_number", mobileNumber)
            apply() // Save the data
        }
    }
}