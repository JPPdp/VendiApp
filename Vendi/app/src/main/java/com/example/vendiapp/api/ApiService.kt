package com.example.vendiapp.api

import com.example.vendiapp.model.*

import retrofit2.Call
import retrofit2.Response
import retrofit2.http.*

interface ApiService {

    @POST("register_client.php")
    fun createClient(@Body clientRequest: ClientRequest): Call<ClientResponse>

    @POST("login_client.php")
    fun getClient(@Body loginRequest: LoginRequest): Call<LoginResponse>
}

interface VendorApiService {

    @GET("vendors.php")
    suspend fun getVendorsByCategory(
        @Query("category") category: String
    ): Response<VendorResponse>  // Note the Response wrapper


}
