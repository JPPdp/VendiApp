package com.example.vendiapp.view.main

import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.EditText
import android.widget.LinearLayout
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.vendiapp.R
import com.example.vendiapp.model.ChatMessage
import com.example.vendiapp.api.ApiUtils
import java.text.SimpleDateFormat
import java.util.*

class ChatFragment : Fragment() {

    private lateinit var rvChat: RecyclerView
    private lateinit var etMessage: EditText
    private lateinit var btnSend: Button
    private lateinit var chatAdapter: ChatAdapter
    private var userId: Int = 0
    private var vendorId: Int = 0
    private val messagesList = mutableListOf<ChatMessage>()
    private val handler = Handler(Looper.getMainLooper())
    private val updateInterval = 5000L // 5 seconds

    private lateinit var chatId: String


    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val view = inflater.inflate(R.layout.fragment_chat, container, false)

        rvChat = view.findViewById(R.id.rvChat)
        etMessage = view.findViewById(R.id.etMessage)
        btnSend = view.findViewById(R.id.btnSend)

        rvChat.layoutManager = LinearLayoutManager(requireContext())
        chatAdapter = ChatAdapter(messagesList)
        rvChat.adapter = chatAdapter

        arguments?.let {
            userId = it.getInt("userId", 0)
            vendorId = it.getInt("vendorId", 0)
        }

        chatId = "user_${userId}_vendor_${vendorId}" // Generate unique chat ID

        loadChatMessages()

        btnSend.setOnClickListener {
            sendMessage()
        }

        val llBack = view.findViewById<LinearLayout>(R.id.llBack)
        llBack.setOnClickListener {
            parentFragmentManager.popBackStack()
        }

        handler.postDelayed(updateRunnable, updateInterval)

        return view
    }

    private val updateRunnable = object : Runnable {
        override fun run() {
            loadChatMessages()
            handler.postDelayed(this, updateInterval)
        }
    }

    private fun loadChatMessages() {
        ApiUtils.getChatMessagesFromDB(chatId) { messages ->
            requireActivity().runOnUiThread {
                messagesList.clear()
                messagesList.addAll(messages)
                chatAdapter.notifyDataSetChanged()
                rvChat.scrollToPosition(messagesList.size - 1)
            }
        }
    }

    private fun sendMessage() {
        val messageText = etMessage.text.toString().trim()
        if (messageText.isEmpty()) {
            Toast.makeText(requireContext(), "Message cannot be empty!", Toast.LENGTH_SHORT).show()
            return
        }

        val message = ChatMessage(
            senderId = "user_$userId",
            message = messageText,
            sentAt = getCurrentTimestamp()
        )

        ApiUtils.sendChatMessageToDB(chatId, message) { success ->
            requireActivity().runOnUiThread {
                if (success) {
                    etMessage.text.clear()
                    messagesList.add(message)
                    chatAdapter.notifyItemInserted(messagesList.size - 1)
                    rvChat.scrollToPosition(messagesList.size - 1)
                } else {
                    Toast.makeText(requireContext(), "Failed to send message.", Toast.LENGTH_SHORT)
                        .show()
                }
            }
        }
    }

    private fun getCurrentTimestamp(): String {
        val sdf = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
        return sdf.format(Date())
    }

    override fun onDestroyView() {
        super.onDestroyView()
        handler.removeCallbacks(updateRunnable)
    }

    companion object {
        fun newInstance(vendorId: Int): ChatFragment {
            return ChatFragment().apply {
                arguments = Bundle().apply {
                    putInt("vendorId", vendorId)
                }
            }
        }
    }
}
