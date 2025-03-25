package com.example.vendiapp.utils

import android.content.Context
import android.util.Log
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import okhttp3.*
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import org.json.JSONObject
import java.io.IOException

object ApiUtils {

    // ✅ Use actual IP address instead of localhost
    private const val BASE_URL = "http://192.168.68.103/vendi-api/api/" // Replace with your IP

    // ✅ Register User to Database
    fun registerUserToDB(context: Context, fullName: String, email: String, phone: String, password: String) {
        val url = "${BASE_URL}register.php"

        val json = JSONObject().apply {
            put("full_name", fullName)
            put("email", email)
            put("phone_number", phone)
            put("password", password)
        }

        val requestBody = RequestBody.create(
            "application/json; charset=utf-8".toMediaTypeOrNull(),
            json.toString()
        )

        val request = Request.Builder()
            .url(url)
            .post(requestBody)
            .build()

        val client = OkHttpClient()

        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                Log.e("API_ERROR", "Failed to connect: ${e.message}")
                (context as? AppCompatActivity)?.runOnUiThread {
                    Toast.makeText(context, "Failed to connect to server.", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onResponse(call: Call, response: Response) {
                response.use { res ->
                    if (!res.isSuccessful) {
                        Log.e("API_ERROR", "Server error: ${res.code}")
                        (context as? AppCompatActivity)?.runOnUiThread {
                            Toast.makeText(context, "Server error: ${res.code}", Toast.LENGTH_SHORT).show()
                        }
                        return
                    }

                    val responseBody = res.body?.string()
                    Log.d("API_RESPONSE", "Response: $responseBody")

                    if (responseBody != null) {
                        try {
                            val jsonResponse = JSONObject(responseBody)
                            val message = jsonResponse.getString("message")

                            (context as? AppCompatActivity)?.runOnUiThread {
                                Toast.makeText(context, message, Toast.LENGTH_SHORT).show()
                            }
                        } catch (e: Exception) {
                            e.printStackTrace()
                            (context as? AppCompatActivity)?.runOnUiThread {
                                Toast.makeText(context, "Invalid response format.", Toast.LENGTH_SHORT).show()
                            }
                        }
                    } else {
                        (context as? AppCompatActivity)?.runOnUiThread {
                            Toast.makeText(context, "Empty response from server.", Toast.LENGTH_SHORT).show()
                        }
                    }
                }
            }
        })
    }

    // ✅ Fetch All Users from Database
    fun getUsersFromDB(context: Context, onResult: (List<User>?) -> Unit) {
        val url = "${BASE_URL}get_users.php"

        val request = Request.Builder()
            .url(url)
            .get()
            .build()

        val client = OkHttpClient()

        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                Log.e("API_ERROR", "Failed to connect: ${e.message}")
                (context as? AppCompatActivity)?.runOnUiThread {
                    Toast.makeText(context, "Failed to connect to server.", Toast.LENGTH_SHORT).show()
                }
                onResult(null)
            }

            override fun onResponse(call: Call, response: Response) {
                response.use { res ->
                    if (!res.isSuccessful) {
                        Log.e("API_ERROR", "Server error: ${res.code}")
                        (context as? AppCompatActivity)?.runOnUiThread {
                            Toast.makeText(context, "Server error: ${res.code}", Toast.LENGTH_SHORT).show()
                        }
                        onResult(null)
                        return
                    }

                    val responseBody = res.body?.string()
                    Log.d("API_RESPONSE", "Response: $responseBody")

                    if (responseBody != null) {
                        try {
                            val jsonArray = JSONObject(responseBody).getJSONArray("users")
                            val userList = mutableListOf<User>()

                            for (i in 0 until jsonArray.length()) {
                                val userObj = jsonArray.getJSONObject(i)
                                val user = User(
                                    userObj.getInt("user_id"),
                                    userObj.getString("full_name"),
                                    userObj.getString("email"),
                                    userObj.getString("phone_number")
                                )
                                userList.add(user)
                            }

                            (context as? AppCompatActivity)?.runOnUiThread {
                                onResult(userList)
                            }

                        } catch (e: Exception) {
                            e.printStackTrace()
                            (context as? AppCompatActivity)?.runOnUiThread {
                                Toast.makeText(context, "Error parsing data.", Toast.LENGTH_SHORT).show()
                            }
                            onResult(null)
                        }
                    } else {
                        (context as? AppCompatActivity)?.runOnUiThread {
                            Toast.makeText(context, "No data received.", Toast.LENGTH_SHORT).show()
                        }
                        onResult(null)
                    }
                }
            }
        })
    }
}

// ✅ User Data Model
data class User(
    val userId: Int,
    val full_name: String,
    val email: String,
    val phone_number: String
)
