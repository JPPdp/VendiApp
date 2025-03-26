package com.example.vendiapp.view.auth

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.*
import androidx.fragment.app.Fragment
import com.example.vendiapp.R
import com.example.vendiapp.api.ApiUtils

class RegistrationFragment3 : Fragment() {

    private lateinit var etEmail: EditText
    private lateinit var etCreatePassword: EditText
    private lateinit var cbTerms: CheckBox
    private lateinit var btnSignIn: Button

    private var username: String? = null
    private var phone: String? = null
    private var email: String? = null
    private var password: String? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_registration3, container, false)

        // ✅ Initialize views
        etEmail = view.findViewById(R.id.etEmail)
        etCreatePassword = view.findViewById(R.id.etCreatePassword)
        cbTerms = view.findViewById(R.id.cbTerms)
        btnSignIn = view.findViewById(R.id.btnSignIn)

        // ✅ Get data from previous fragments
        username = arguments?.getString("username")
        phone = arguments?.getString("phone")

        // ✅ Disable sign-in button until terms are accepted
        btnSignIn.isEnabled = false
        cbTerms.setOnCheckedChangeListener { _, isChecked ->
            btnSignIn.isEnabled = isChecked
        }

        // ✅ Handle sign-in button click
        btnSignIn.setOnClickListener {
            email = etEmail.text.toString().trim()
            password = etCreatePassword.text.toString().trim()

            if (validateInputs(email, password)) {
                registerUser(username!!, email!!, phone!!, password!!)
            }
        }

        // ✅ Handle back button click
        val llBack = view.findViewById<LinearLayout>(R.id.llBack)
        llBack?.setOnClickListener {
            parentFragmentManager.popBackStack()
        }

        return view
    }

    // ✅ Validate email and password inputs
    private fun validateInputs(email: String?, password: String?): Boolean {
        if (email.isNullOrEmpty() || !isValidEmail(email)) {
            etEmail.error = "Please enter a valid email!"
            return false
        }
        if (password.isNullOrEmpty() || password.length < 8) {
            etCreatePassword.error = "Password must be at least 8 characters"
            return false
        }
        return true
    }

    // ✅ Register user through API
    private fun registerUser(username: String, email: String, phone: String, password: String) {
        ApiUtils.registerUserToDB(
            username,
            email,
            phone,
            password
        ) { success, message ->
            requireActivity().runOnUiThread {
                if (success) {
                    Toast.makeText(context, "Registration Successful!", Toast.LENGTH_SHORT).show()

                    // ✅ Navigate to login after successful registration
                    parentFragmentManager.beginTransaction()
                        .replace(R.id.fgtContainer, LogInFragment())
                        .commit()
                } else {
                    Toast.makeText(context, message, Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    // ✅ Validate email format
    private fun isValidEmail(email: String): Boolean {
        return android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches()
    }
}
