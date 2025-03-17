package com.example.vendiapp.view.auth

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.EditText
import android.widget.TextView
import androidx.fragment.app.Fragment
import androidx.fragment.app.viewModels
import com.example.vendiapp.R
import com.example.vendiapp.view.main.MainActivity
import com.example.vendiapp.viewmodel.LogInViewModel

class LogInFragment : Fragment() {

    private val loginViewModel: LogInViewModel by viewModels()

    override fun onCreateView(inflater: LayoutInflater, container: ViewGroup?,
                              savedInstanceState: Bundle?): View? {
        val view = inflater.inflate(R.layout.fragment_log_in, container, false)

        val btnSignIn: Button = view.findViewById(R.id.btnSignIn)
        val btnSignUp: Button = view.findViewById(R.id.btnSignUp)
        val tvForgotPassword: TextView = view.findViewById(R.id.tvForgotPassword)
      //  val etEmail: EditText = view.findViewById(R.id.etEmail)
       // val etPassword: EditText = view.findViewById(R.id.etPassword)

        btnSignIn.setOnClickListener {
//            val email = etEmail.text.toString()
//            val password = etPassword.text.toString()
//            loginViewModel.loginUser(email, password)
            val intent = Intent(activity, MainActivity::class.java)
            startActivity(intent)
            requireActivity().finish() // Close AuthActivity after login
        }

//        loginViewModel.loginResult.observe(viewLifecycleOwner) { success ->
//            if (success) {
//                val intent = Intent(activity, MainActivity::class.java)
//                startActivity(intent)
//                requireActivity().finish() // Close AuthActivity after login
//            } else {
//                Toast.makeText(requireContext(), "Login failed!", Toast.LENGTH_SHORT).show()
//            }
//        }

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
}
