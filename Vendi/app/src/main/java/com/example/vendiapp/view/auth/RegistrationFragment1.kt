package com.example.vendiapp.view.auth

import com.example.vendiapp.view.auth.RegistrationFragment1
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.EditText
import android.widget.LinearLayout
import android.widget.Toast
import androidx.fragment.app.Fragment
import com.example.vendiapp.R
import com.example.vendiapp.view.auth.PasswordRecoveryFragment
class RegistrationFragment1 : Fragment() {

    private lateinit var etCall: EditText
    private var username: String? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_registration1, container, false)

        etCall = view.findViewById(R.id.etCall)
        val btnNext: Button = view.findViewById(R.id.btnNext)

        btnNext.setOnClickListener {
            username = etCall.text.toString().trim()

            if (username.isNullOrEmpty()) {
                Toast.makeText(context, "Enter a username!", Toast.LENGTH_SHORT).show()
            } else {
                val fragment = RegistrationFragment2()
                val bundle = Bundle()
                bundle.putString("username", username)
                fragment.arguments = bundle
                parentFragmentManager.beginTransaction()
                    .replace(R.id.fgtContainer, fragment)
                    .addToBackStack(null)
                    .commit()
            }
        }

        val llBack = view.findViewById<LinearLayout>(R.id.llBack)
        llBack.setOnClickListener {
            parentFragmentManager.popBackStack()
        }

        return view
    }
}
