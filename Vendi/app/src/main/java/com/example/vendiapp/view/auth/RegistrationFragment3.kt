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
        val llBack = view.findViewById<LinearLayout>(R.id.llBack)
        llBack?.setOnClickListener {
            requireActivity().onBackPressedDispatcher.onBackPressed()
        }

        return view
    }

    // Validate email and password inputs
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

    // Function to check email validity
    private fun isValidEmail(email: String): Boolean {
        return android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches()
    }

    // Register user through API
    private fun registerUser(email: String, password: String) {
        val request = ClientRequest(
            name = username ?: "",
            email = email,
            mobile_number = phone ?: "",
            password = password
        )

        RetrofitClient.instance.createClient(request).enqueue(object : Callback<ClientResponse> {
            override fun onResponse(call: Call<ClientResponse>, response: Response<ClientResponse>) {
                if (response.isSuccessful) {
                    // Log the raw response body to debug what the server is sending back
                    Log.d("API_RESPONSE", "Response body: ${response.body()}")

                    // Continue with your existing logic
                    if (response.body() != null) {
                        val signupResponse = response.body()!!
                        if (signupResponse.success) {
                            Toast.makeText(requireContext(), "Signup successful!", Toast.LENGTH_SHORT).show()
                            parentFragmentManager.beginTransaction()
                                .replace(R.id.fgtContainer, LogInFragment())
                                .commit()
                        } else {
                            Toast.makeText(requireContext(), signupResponse.message ?: "Signup failed", Toast.LENGTH_SHORT).show()
                        }
                    }
                } else {
                    // Log the error body to debug the error response
                    val errorBody = response.errorBody()?.string()
                    Log.e("API_ERROR", "Error response body: $errorBody")
                    handleErrorResponse(errorBody)
                }
            }



            override fun onFailure(call: Call<ClientResponse>, t: Throwable) {
                Log.e("NETWORK_ERROR", "Failed to make request: ${t.message}")
                Toast.makeText(requireContext(), "Network error: ${t.message}", Toast.LENGTH_LONG).show()
            }
        })
    }

    // Handle error response from API
    private fun handleErrorResponse(error: String?) {
        Log.e("API_ERROR", "Error Response: $error")
        Toast.makeText(requireContext(), "Error: $error", Toast.LENGTH_LONG).show()
    }
}
