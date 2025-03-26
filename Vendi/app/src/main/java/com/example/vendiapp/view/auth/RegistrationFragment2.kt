package com.example.vendiapp.view.auth

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.fragment.app.Fragment
import com.example.vendiapp.R

class RegistrationFragment2 : Fragment() {

    private lateinit var etPhone: EditText
    private lateinit var btnNext: Button
    private var username: String? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_registration2, container, false)

        etPhone = view.findViewById(R.id.etPhone)
        btnNext = view.findViewById(R.id.btnNext)

        // ✅ Get data from RegistrationFragment1
        username = arguments?.getString("username")

        // ✅ Handle Next button click
        btnNext.setOnClickListener {
            val phone = etPhone.text.toString().trim()

            if (phone.isEmpty() || phone.length < 10) {
                etPhone.error = "Please enter a valid phone number!"
            } else {
                val bundle = Bundle().apply {
                    putString("username", username)
                    putString("phone", phone)
                }
                val fragment = RegistrationFragment3()
                fragment.arguments = bundle

                parentFragmentManager.beginTransaction()
                    .replace(R.id.fgtContainer, fragment)
                    .addToBackStack(null)
                    .commit()
            }
        }

        return view
    }
}
