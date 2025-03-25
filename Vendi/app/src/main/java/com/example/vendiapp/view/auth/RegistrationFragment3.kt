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

    private lateinit var etCreatePassword: EditText
    private lateinit var cbTerms: CheckBox
    private lateinit var btnSignIn: Button

    private var username: String? = null
    private var phone: String? = null
    private var password: String? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_registration3, container, false)

        etCreatePassword = view.findViewById(R.id.etCreatePassword)
        cbTerms = view.findViewById(R.id.cbTerms)
        btnSignIn = view.findViewById(R.id.btnSignIn)

        username = arguments?.getString("username")
        phone = arguments?.getString("phone")

        // Disable sign-in until terms are checked
        btnSignIn.isEnabled = false
        cbTerms.setOnCheckedChangeListener { _, isChecked ->
            btnSignIn.isEnabled = isChecked
        }

        // ✅ Handle sign-in click
        btnSignIn.setOnClickListener {
            password = etCreatePassword.text.toString().trim()

            if (password.isNullOrEmpty() || password!!.length < 8) {
                etCreatePassword.error = "Password must be at least 8 characters"
            } else {
                registerUser(username!!, phone!!, password!!)
            }
        }

        val llBack = view.findViewById<LinearLayout>(R.id.llBack)
        llBack.setOnClickListener {
            parentFragmentManager.popBackStack()
        }

        return view
    }

    // ✅ Register user through API
    private fun registerUser(username: String, phone: String, password: String) {
        ApiUtils.registerUserToDB(username, phone, password) { success, message ->
            requireActivity().runOnUiThread {
                if (success) {
                    Toast.makeText(context, "Registration Successful!", Toast.LENGTH_SHORT).show()

                    // Navigate to login after registration
                    parentFragmentManager.beginTransaction()
                        .replace(R.id.fgtContainer, LogInFragment())
                        .commit()
                } else {
                    Toast.makeText(context, message, Toast.LENGTH_SHORT).show()
                }
            }
        }
    }
}
