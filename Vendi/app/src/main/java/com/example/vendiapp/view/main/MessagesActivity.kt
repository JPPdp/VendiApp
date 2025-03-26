package com.example.vendiapp.view.main

import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.vendiapp.R
import com.example.vendiapp.adapter.MessageAdapter
import com.example.vendiapp.api.ApiUtils
import com.example.vendiapp.model.MessageModel
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import com.example.vendiapp.api.ApiService

class MessagesActivity : AppCompatActivity() {

    private lateinit var rvMessages: RecyclerView
    private lateinit var messageAdapter: MessageAdapter
    private var messageList = mutableListOf<MessageModel>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_messages)

        // ✅ Initialize RecyclerView and Adapter
        rvMessages = findViewById(R.id.rvMessages)
        rvMessages.layoutManager = LinearLayoutManager(this)

        // ✅ Initialize adapter with an empty list
        messageAdapter = MessageAdapter(messageList)
        rvMessages.adapter = messageAdapter

        // ✅ Load messages from API
        loadMessages()
    }

    private fun loadMessages() {
        // ✅ Correctly access ApiService
        val apiService = ApiUtils.apiService

        // ✅ Fetch messages from API
        apiService.getAllMessages().enqueue(object : Callback<List<MessageModel>> {
            override fun onResponse(
                call: Call<List<MessageModel>>,
                response: Response<List<MessageModel>>
            ) {
                if (response.isSuccessful && response.body() != null) {
                    messageList.clear()
                    messageList.addAll(response.body()!!)
                    messageAdapter.notifyDataSetChanged()
                } else {
                    showToast("Failed to load messages")
                }
            }

            override fun onFailure(call: Call<List<MessageModel>>, t: Throwable) {
                showToast("Error: ${t.message}")
            }
        })
    }

    // ✅ Show toast message
    private fun showToast(message: String) {
        Toast.makeText(this, message, Toast.LENGTH_SHORT).show()
    }
}
