package com.example.vendiapp.model

data class ChatMessage(
    val senderId: String,   // Either "user_{userId}" or "vendor_{vendorId}"
    val message: String,    // Message content
    val sentAt: String      // Timestamp in "yyyy-MM-dd HH:mm:ss"
)
