package com.example.vendiapp.model

// ✅ Chat Message Data Model
data class ChatMessage(
    val senderId: String,
    val message: String,
    val sentAt: String
)
