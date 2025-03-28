package com.example.vendiapp.view.main.profile

import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.LinearLayout
import com.example.vendiapp.R

class AccountInfoFragment : Fragment() {

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_account_info, container, false)

        // Handle llBack click to navigate back
        val llBack = view.findViewById<LinearLayout>(R.id.llBack)
        llBack.setOnClickListener {
            parentFragmentManager.popBackStack() // Go back to the previous fragment
        }

        return view
    }
}
