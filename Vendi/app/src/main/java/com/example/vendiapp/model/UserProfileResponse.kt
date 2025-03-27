package com.example.vendiapp.model

import com.google.gson.annotations.SerializedName

data class UserProfileResponse(
    @SerializedName("user_id") val user_id: String,
    @SerializedName("full_name") val full_name: String?,
    @SerializedName("email") val email: String?,
    @SerializedName("phone_number") val phone_number: String?,
    @SerializedName("password") val password: String?,
    @SerializedName("created_at") val created_at: String?
)