package com.example.vendiapp.view.auth

import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.*
import androidx.fragment.app.Fragment
import com.example.vendiapp.R
import com.example.vendiapp.api.RetrofitClient
import com.example.vendiapp.model.ClientResponse
import com.example.vendiapp.model.ClientRequest
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.io.IOException

class RegistrationFragment3 : Fragment() {

    private lateinit var etEmail: EditText
    private lateinit var etCreatePassword: EditText
    private lateinit var cbTerms: CheckBox
    private lateinit var btnSignIn: Button

    private var username: String? = null
    private var phone: String? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_registration3, container, false)

        // Initialize views
        etEmail = view.findViewById(R.id.etEmail)
        etCreatePassword = view.findViewById(R.id.etCreatePassword)
        cbTerms = view.findViewById(R.id.cbTerms)
        btnSignIn = view.findViewById(R.id.btnSignIn)

        // Get data from previous fragments
        arguments?.let {
            username = it.getString("username")
            phone = it.getString("phone")
        }

        // Disable sign-in button until terms are accepted
        btnSignIn.isEnabled = false
        cbTerms.setOnCheckedChangeListener { _, isChecked ->
            btnSignIn.isEnabled = isChecked
        }

        // Handle sign-in button click
        btnSignIn.setOnClickListener {
            val email = etEmail.text.toString().trim()
            val password = etCreatePassword.text.toString().trim()

            if (validateInputs(email, password)) {
                registerUser(email, password)
            }
        }

        // Handle back button click
        view.findViewById<LinearLayout>(R.id.llBack)?.setOnClickListener {
            requireActivity().onBackPressedDispatcher.onBackPressed()
        }

        return view
    }

    private fun validateInputs(email: String, password: String): Boolean {
        return when {
            email.isEmpty() || !isValidEmail(email) -> {
                etEmail.error = "Please enter a valid email!"
                false
            }
            password.isEmpty() || password.length < 8 -> {
                etCreatePassword.error = "Password must be at least 8 characters"
                false
            }
            else -> true
        }
    }

    private fun isValidEmail(email: String): Boolean {
        return android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches()
    }

    private fun registerUser(email: String, password: String) {
        val request = ClientRequest(
            name = username ?: "",
            email = email,
            mobile_number = phone ?: "",
            password = password
        )

        showLoading(true)

        RetrofitClient.instance.createClient(request).enqueue(object : Callback<ClientResponse> {
            override fun onResponse(call: Call<ClientResponse>, response: Response<ClientResponse>) {
                showLoading(false)

                try {
                    if (response.isSuccessful) {
                        response.body()?.let { apiResponse ->
                            if (apiResponse.success) {
                                handleSuccess()
                            } else {
                                showError(apiResponse.message ?: "Registration failed")
                            }
                        } ?: showError("Empty response from server")
                    } else {
                        // Try to parse error response
                        val errorBody = response.errorBody()?.string()
                        Log.e("API_ERROR", "Status code: ${response.code()}, Error: $errorBody")

                        try {
                            // If your server returns consistent error format
                            val errorResponse = RetrofitClient.gson.fromJson(errorBody, ClientResponse::class.java)
                            showError(errorResponse.message ?: "Error: ${response.code()}")
                        } catch (e: Exception) {
                            // Fallback to raw error message
                            showError(errorBody ?: "Error: ${response.code()}")
                        }
                    }
                } catch (e: Exception) {
                    Log.e("API_ERROR", "Response processing error", e)
                    showError("Error processing response")
                }
            }

            override fun onFailure(call: Call<ClientResponse>, t: Throwable) {
                showLoading(false)
                Log.e("NETWORK_ERROR", "API call failed", t)
                showError(
                    when (t) {
                        is IOException -> "Network error. Please check your connection"
                        else -> "Error: ${t.localizedMessage}"
                    }
                )
            }
        })
    }

    private fun handleSuccess() {
        Toast.makeText(requireContext(), "Registration successful!", Toast.LENGTH_SHORT).show()
        parentFragmentManager.beginTransaction()
            .replace(R.id.fgtContainer, LogInFragment())
            .commit()
    }

    private fun showError(message: String) {
        Toast.makeText(requireContext(), message, Toast.LENGTH_LONG).show()
    }

    private fun showLoading(show: Boolean) {
        btnSignIn.isEnabled = !show
        btnSignIn.text = if (show) "Processing..." else "Sign In"
        // You can add a progress bar here if needed
    }
}