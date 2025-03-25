package com.example.vendiapp.api

import android.content.Context
import android.util.Log
import okhttp3.*
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.RequestBody.Companion.toRequestBody
import org.json.JSONArray
import org.json.JSONObject
import com.example.vendiapp.model.ChatMessage
import java.io.IOException

object ApiUtils {
    private const val BASE_URL = "http://192.168.68.103/VendiApp/api/" // ✅ Change this if needed!

    private val client = OkHttpClient()

    // ✅ Get chat messages from DB
    fun getChatMessagesFromDB(chatId: String, callback: (List<ChatMessage>) -> Unit) {
        val url = "${BASE_URL}get_messages.php?chat_id=$chatId"
        val request = Request.Builder().url(url).build()

        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                Log.e("ApiUtils", "Error loading messages: ${e.message}")
                callback(emptyList())
            }

            override fun onResponse(call: Call, response: Response) {
                if (response.isSuccessful) {
                    val messages = mutableListOf<ChatMessage>()
                    response.body?.string()?.let {
                        val jsonArray = JSONArray(it)
                        for (i in 0 until jsonArray.length()) {
                            val obj = jsonArray.getJSONObject(i)
                            messages.add(
                                ChatMessage(
                                    senderId = obj.getString("sender_id"),
                                    message = obj.getString("message_text"),
                                    sentAt = obj.getString("sent_at")
                                )
                            )
                        }
                    }
                    callback(messages)
                } else {
                    Log.e("ApiUtils", "Error retrieving messages: ${response.code}")
                    callback(emptyList())
                }
            }
        })
    }

    // ✅ Send chat message to DB
    fun sendChatMessageToDB(chatId: String, message: ChatMessage, callback: (Boolean) -> Unit) {
        val url = "${BASE_URL}send_message.php"
        val requestBody = FormBody.Builder()
            .add("chat_id", chatId)
            .add("sender_id", message.senderId)
            .add("message_text", message.message)
            .build()

        val request = Request.Builder().url(url).post(requestBody).build()

        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                Log.e("ApiUtils", "Error sending message: ${e.message}")
                callback(false)
            }

            override fun onResponse(call: Call, response: Response) {
                callback(response.isSuccessful)
            }
        })
    }
    // ✅ Login method
    fun loginUserToDB(email: String, password: String, callback: (Boolean, String?, String) -> Unit) {
        val url = "$BASE_URL/login"
        val json = JSONObject().apply {
            put("email", email)
            put("password", password)
        }

        val requestBody = json.toString().toRequestBody("application/json".toMediaTypeOrNull())
        val request = Request.Builder().url(url).post(requestBody).build()

        val client = OkHttpClient()
        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                callback(false, null, "Network error! Try again.")
            }

            override fun onResponse(call: Call, response: Response) {
                val body = response.body?.string()
                if (response.isSuccessful && !body.isNullOrEmpty()) {
                    val jsonResponse = JSONObject(body)
                    val userId = jsonResponse.optString("userId")
                    callback(true, userId, "Login successful!")
                } else {
                    callback(false, null, "Invalid credentials. Please try again!")
                }
            }
        })
    }

    // ✅ Register method
    fun registerUserToDB(username: String, phone: String, password: String, callback: (Boolean, String) -> Unit) {
        val url = "$BASE_URL/register"
        val json = JSONObject().apply {
            put("username", username)
            put("phone", phone)
            put("password", password)
        }

        val requestBody = json.toString().toRequestBody("application/json".toMediaTypeOrNull())
        val request = Request.Builder().url(url).post(requestBody).build()

        val client = OkHttpClient()
        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                callback(false, "Network error! Please try again.")
            }

            override fun onResponse(call: Call, response: Response) {
                val body = response.body?.string()
                if (response.isSuccessful) {
                    callback(true, "Registration successful!")
                } else {
                    callback(false, "Error! ${response.message}")
                }
            }
        })
    }

    // ✅ Book an event
    fun bookEventToDB(context: Context, eventId: Int, vendorId: Int, callback: (Boolean, String) -> Unit) {
        val url = "${BASE_URL}bookEvent.php"
        val jsonObject = JSONObject().apply {
            put("event_id", eventId)
            put("vendor_id", vendorId)
        }

        val requestBody = jsonObject.toString().toRequestBody("application/json".toMediaTypeOrNull())

        val request = Request.Builder()
            .url(url)
            .post(requestBody)
            .build()

        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                callback(false, "Booking failed: ${e.message}")
            }

            override fun onResponse(call: Call, response: Response) {
                response.use { res ->
                    val message =
                        if (res.isSuccessful) "Booking successful!" else "Booking failed: ${res.body?.string()}"
                    callback(res.isSuccessful, message)
                }
            }
        })
    }
}
