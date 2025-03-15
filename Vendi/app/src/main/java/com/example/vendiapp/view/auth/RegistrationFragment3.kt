package com.example.vendiapp.view.auth

import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.CheckBox
import android.widget.LinearLayout
import com.example.vendiapp.R

class RegistrationFragment3 : Fragment() {


    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        // Inflate the layout for this fragment
        val view = inflater.inflate(R.layout.fragment_registration3, container, false)

        val btnSignIn: Button = view.findViewById(R.id.btnSignIn)
        val cbTerms: CheckBox = view.findViewById(R.id.cbTerms)
//        val etCreatePassword: EditText = view.findViewById(R.id.etCreatePassword)

//        btnSignIn.isEnabled = false

        cbTerms.setOnCheckedChangeListener { _, isChecked ->
            btnSignIn.isEnabled = isChecked
        }

        btnSignIn.setOnClickListener {

//            val password = etCreatePassword.text.toString()

//            if (!isValidPassword(password)) {
//                etCreatePassword.error = "Need a strong password!" // Show error if password is weak
//            } else {
                // Navigate to previous registration fragment
                val fragment = LogInFragment()
                parentFragmentManager.beginTransaction()
                    .replace(R.id.fgtContainer, fragment)
                    .addToBackStack(null)
                    .commit()
//            }
        }

        val llBack = view.findViewById<LinearLayout>(R.id.llBack)
        llBack.setOnClickListener {
            parentFragmentManager.popBackStack() // Go back to the previous fragment
        }

        return view

    }

//    fun openPrivacy(view: View) {
//        val intent = Intent(Intent.ACTION_VIEW, Uri.parse("https://xxx.com"))
//        startActivity(intent)
//    }
//
//    fun openTerms(view: View) {
//        val intent = Intent(Intent.ACTION_VIEW, Uri.parse("https://xxx.com"))
//        startActivity(intent)
//    }
//
//    private fun isValidPassword(password: String): Boolean {
//        return password.length >= 8 // Example rule: At least 8 characters
//    }

}