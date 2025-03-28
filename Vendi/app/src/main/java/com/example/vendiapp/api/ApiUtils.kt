package com.example.vendiapp.api

import android.util.Log
import com.example.vendiapp.model.*
import okhttp3.OkHttpClient
import okhttp3.Request
import org.json.JSONObject
import retrofit2.*
import retrofit2.converter.gson.GsonConverterFactory
import java.net.HttpURLConnection
import java.net.URL

object ApiUtils {

    // ✅ Base URL for all API requests
    const val BASE_URL = "http://192.168.0.49/vendi-api/api/"
    const val LOGIN_URL = BASE_URL + "login.php"

    // ✅ Retrofit instance
    private val retrofit: Retrofit by lazy {
        Retrofit.Builder()
            .baseUrl(BASE_URL)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
    }

    // ✅ ApiService instance (Use this for Retrofit calls)
    val apiService: ApiService by lazy {
        retrofit.create(ApiService::class.java)
    }

    // ✅ Create new ApiService instance (FIXED)
    fun createApiService(): ApiService {
        val retrofit = Retrofit.Builder()
            .baseUrl(BASE_URL)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
        return retrofit.create(ApiService::class.java)
    }

    // ✅ Register User using Retrofit
    fun registerUserToDB(
        fullName: String,
        email: String,
        phone: String,
        password: String,
        callback: (Boolean, String) -> Unit
    ) {
        val call = apiService.registerUser(fullName, email, phone, password)
        call.enqueue(object : Callback<GenericResponse> {
            override fun onResponse(call: Call<GenericResponse>, response: Response<GenericResponse>) {
                if (response.isSuccessful) {
                    callback(true, response.body()?.message ?: "Registration successful!")
                } else {
                    callback(false, "Failed to register. Please try again!")
                }
            }

            override fun onFailure(call: Call<GenericResponse>, t: Throwable) {
                callback(false, "Error: ${t.message}")
            }
        })
    }

    // ✅ Login User using HttpURLConnection
    fun loginUserToDB(email: String, password: String, callback: (Boolean, String?, String) -> Unit) {
        val url = LOGIN_URL
        val requestBody = "email=$email&password=$password".toByteArray()

        Thread {
            try {
                val conn = (URL(url).openConnection() as HttpURLConnection).apply {
                    requestMethod = "POST"
                    setRequestProperty("Content-Type", "application/x-www-form-urlencoded")
                    doOutput = true
                    outputStream.write(requestBody)
                }

                val responseCode = conn.responseCode
                val response = conn.inputStream.bufferedReader().use { it.readText() }

                Log.d("API_RESPONSE", "Response: $response")

                if (responseCode == 200) {
                    val jsonResponse = JSONObject(response)
                    if (jsonResponse.getString("status") == "success") {
                        val userId = jsonResponse.getString("user_id")
                        callback(true, userId, "Login successful!")
                    } else {
                        val message = jsonResponse.getString("message")
                        callback(false, null, message)
                    }
                } else {
                    callback(false, null, "Error: ${conn.responseMessage}")
                }
            } catch (e: Exception) {
                Log.e("API_ERROR", "Error: ${e.localizedMessage}")
                callback(false, null, "Network error! Please try again.")
            }
        }.start()
    }

    // ✅ Fetch All Users using Retrofit
    fun getUsersFromDB(callback: (List<User>?) -> Unit) {
        val call = apiService.getAllUsers()
        call.enqueue(object : Callback<List<User>> {
            override fun onResponse(call: Call<List<User>>, response: Response<List<User>>) {
                if (response.isSuccessful) {
                    callback(response.body())
                } else {
                    callback(null)
                }
            }

            override fun onFailure(call: Call<List<User>>, t: Throwable) {
                Log.e("API_ERROR", "Failed to fetch users: ${t.message}")
                callback(null)
            }
        })
    }

    // ✅ Fetch All Messages using Retrofit
    fun getAllMessagesFromDB(callback: (List<MessageModel>?) -> Unit) {
        val call = apiService.getAllMessages()
        call.enqueue(object : Callback<List<MessageModel>> {
            override fun onResponse(call: Call<List<MessageModel>>, response: Response<List<MessageModel>>) {
                if (response.isSuccessful) {
                    callback(response.body())
                } else {
                    callback(null)
                }
            }

            override fun onFailure(call: Call<List<MessageModel>>, t: Throwable) {
                Log.e("API_ERROR", "Failed to load messages: ${t.message}")
                callback(null)
            }
        })
    }

    // ✅ Fetch Chat Messages using Retrofit
    fun getChatMessagesFromDB(chatId: String, callback: (List<ChatMessage>) -> Unit) {
        val call = apiService.getChatMessages(chatId)
        call.enqueue(object : Callback<List<ChatMessage>> {
            override fun onResponse(call: Call<List<ChatMessage>>, response: Response<List<ChatMessage>>) {
                if (response.isSuccessful) {
                    callback(response.body() ?: emptyList())
                } else {
                    callback(emptyList())
                }
            }

            override fun onFailure(call: Call<List<ChatMessage>>, t: Throwable) {
                Log.e("ApiUtils", "Error loading messages: ${t.message}")
                callback(emptyList())
            }
        })
    }

    // ✅ Send Chat Message using Retrofit
    fun sendChatMessageToDB(chatId: String, message: ChatMessage, callback: (Boolean) -> Unit) {
        val call = apiService.sendChatMessage(chatId, message.senderId, message.message)
        call.enqueue(object : Callback<GenericResponse> {
            override fun onResponse(call: Call<GenericResponse>, response: Response<GenericResponse>) {
                callback(response.isSuccessful)
            }

            override fun onFailure(call: Call<GenericResponse>, t: Throwable) {
                Log.e("ApiUtils", "Error sending message: ${t.message}")
                callback(false)
            }
        })
    }

    // ✅ Fetch User Profile using OkHttpClient
    fun getUserProfile(userId: String, callback: (Boolean, String?, String?, String) -> Unit) {
        val url = "${BASE_URL}profile.php?userId=$userId"

        val client = OkHttpClient()

        val request = Request.Builder()
            .url(url)
            .get()
            .build()

        Thread {
            try {
                val response = client.newCall(request).execute()

                if (response.isSuccessful) {
                    response.body?.let { responseBody ->
                        val data = JSONObject(responseBody.string())
                        val profileName = data.getString("profileName")
                        val profileEmail = data.getString("profileEmail")
                        callback(true, profileName, profileEmail, "Profile fetched successfully.")
                    } ?: run {
                        callback(false, null, null, "Error: Empty response body.")
                    }
                } else {
                    callback(false, null, null, "Error fetching profile. Code: ${response.code}")
                }
            } catch (e: Exception) {
                e.printStackTrace()
                callback(false, null, null, "Error: ${e.localizedMessage}")
            }
        }.start()
    }

    // ✅ Book an Event using Retrofit
    fun bookEventToDB(eventId: Int, vendorId: Int, callback: (Boolean, String) -> Unit) {
        val call = apiService.bookEvent(eventId, vendorId)
        call.enqueue(object : Callback<GenericResponse> {
            override fun onResponse(call: Call<GenericResponse>, response: Response<GenericResponse>) {
                if (response.isSuccessful) {
                    callback(true, response.body()?.message ?: "Booking successful!")
                } else {
                    callback(false, "Failed to book event. Try again!")
                }
            }

            override fun onFailure(call: Call<GenericResponse>, t: Throwable) {
                callback(false, "Booking failed: ${t.message}")
            }
        })
    }
}
