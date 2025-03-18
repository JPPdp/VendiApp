package com.example.vendiapp.api

import com.example.vendiapp.model.ApiResponse
import com.example.vendiapp.model.User
import retrofit2.Call
import retrofit2.http.*

interface ApiService {

    interface ApiService {

        @FormUrlEncoded
        @POST("add_user.php")
        fun addUser(
            @Field("full_name") fullName: String,
            @Field("email") email: String,
            @Field("phone_number") phoneNumber: String,
            @Field("password") password: String
        ): Call<ApiResponse>

        @GET("get_users.php")
        fun getUsers(): Call<List<User>>
    }
}