package com.example.vendiapp

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import androidx.appcompat.app.AppCompatActivity

class OnboardingActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_onboarding)

        val btnStart = findViewById<Button>(R.id.btnStart)



        btnStart.setOnClickListener{
            val registerIntent = Intent(this, LogInActivity::class.java)
            startActivity(registerIntent)
            finish()
        }
    }
}