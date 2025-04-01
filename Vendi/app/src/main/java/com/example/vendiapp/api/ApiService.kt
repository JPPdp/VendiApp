package com.example.vendiapp.api

import com.example.vendiapp.model.LoginRequest
import com.example.vendiapp.model.LoginResponse
import com.example.vendiapp.model.ClientRequest
import com.example.vendiapp.model.ClientResponse
import retrofit2.Call
import retrofit2.http.*

interface ApiService {

    @POST("register_client.php")
    fun createClient(@Body clientRequest: ClientRequest): Call<ClientResponse>

    @POST("login.php")
    fun loginUser(@Body registerRequest: LoginRequest): Call<LoginResponse>



}