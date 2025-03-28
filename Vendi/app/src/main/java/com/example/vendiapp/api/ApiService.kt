package com.example.vendiapp.api

import com.example.vendiapp.model.ChatMessage
import com.example.vendiapp.model.GenericResponse
import com.example.vendiapp.model.MessageModel
import com.example.vendiapp.model.ProfileResponse
import com.example.vendiapp.model.User
import com.example.vendiapp.model.UserProfileResponse
import retrofit2.Call
import retrofit2.http.*

interface ApiService {

    // ✅ Register User
    @POST("register.php")
    @FormUrlEncoded
    fun registerUser(
        @Field("full_name") fullName: String,
        @Field("email") email: String,
        @Field("phone_number") phone: String,
        @Field("password") password: String
    ): Call<GenericResponse>

    // ✅ Login User
    @POST("login.php")
    @FormUrlEncoded
    fun loginUser(
        @Field("email") email: String,
        @Field("password") password: String
    ): Call<GenericResponse>

    // ✅ Get All Users
    @GET("get_users.php")
    fun getAllUsers(): Call<List<User>>

    // ✅ Get All Messages (NEW)
    @GET("getAllMessages.php")
    fun getAllMessages(): Call<List<MessageModel>>

    // ✅ Get Chat Messages
    @GET("get_messages.php")
    fun getChatMessages(
        @Query("chat_id") chatId: String
    ): Call<List<ChatMessage>>

    // ✅ Send Chat Message
    @POST("send_message.php")
    @FormUrlEncoded
    fun sendChatMessage(
        @Field("chat_id") chatId: String,
        @Field("sender_id") senderId: String,
        @Field("message_text") message: String
    ): Call<GenericResponse>

    // ✅ Book an Event
    @POST("bookEvent.php")
    @FormUrlEncoded
    fun bookEvent(
        @Field("event_id") eventId: Int,
        @Field("vendor_id") vendorId: Int
    ): Call<GenericResponse>

    @GET("profile") // Update with your correct endpoint
    fun getProfile(): Call<ProfileResponse>

    @GET("get_user_profile.php")
    fun getUserProfile(@Query("user_id") userId: String): Call<UserProfileResponse>
}
