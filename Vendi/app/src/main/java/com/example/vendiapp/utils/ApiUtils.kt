package com.example.vendiapp.utils

import android.content.Context
import android.widget.Toast
import com.example.vendiapp.api.ApiClient
import com.example.vendiapp.model.ApiResponse
import com.example.vendiapp.model.User
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

object ApiUtils {

    fun addUser(
        context: Context,
        fullName: String,
        email: String,
        phoneNumber: String,
        password: String
    ) {
        ApiClient.instance.addUser(fullName, email, phoneNumber, password)
            .enqueue(object : Callback<ApiResponse> {
                override fun onResponse(call: Call<ApiResponse>, response: Response<ApiResponse>) {
                    if (response.isSuccessful) {
                        Toast.makeText(context, response.body()?.message, Toast.LENGTH_LONG).show()
                    } else {
                        Toast.makeText(context, "Error adding user", Toast.LENGTH_LONG).show()
                    }
                }

                override fun onFailure(call: Call<ApiResponse>, t: Throwable) {
                    Toast.makeText(context, "Error: ${t.message}", Toast.LENGTH_LONG).show()
                }
            })
    }
}