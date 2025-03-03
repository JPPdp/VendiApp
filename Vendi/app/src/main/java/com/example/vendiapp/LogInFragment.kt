package com.example.vendiapp

import android.content.Intent
import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.TextView

class LogInFragment : Fragment() {

    override fun onCreateView(inflater: LayoutInflater, container: ViewGroup?,
                              savedInstanceState: Bundle?): View? {
        // Inflate the layout for this fragment
        val view = inflater.inflate(R.layout.fragment_log_in, container, false)

        val btnSignIn: Button = view.findViewById(R.id.btnSignIn)
        val btnSignUp: Button = view.findViewById(R.id.btnSignUp)
        val tvForgotPassword: TextView = view.findViewById(R.id.tvForgotPassword)


        btnSignIn.setOnClickListener {
            val intent = Intent(activity, MainActivity::class.java)
            startActivity(intent)
        }

        btnSignUp.setOnClickListener {
            val intent = Intent(activity, RegistrationActivity::class.java)
            startActivity(intent)
        }

        tvForgotPassword.setOnClickListener {
            val fragment = PasswordRecoveryFragment()
            parentFragmentManager.beginTransaction()
                .replace(R.id.fgtContainer, fragment) // Ensure R.id.fgtContainer is the correct fragment container
                .addToBackStack(null) // Allows back navigation
                .commit()
        }

        return view
    }

}