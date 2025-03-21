package com.example.vendiapp.network

import com.example.vendiapp.model.Product
import retrofit2.Call
import retrofit2.http.GET
import retrofit2.http.Query

interface ApiService {

    @GET("fetch_products.php")
    fun getProductsByCategory(
        @Query("category_id") categoryId: Int,
        @Query("limit") limit: Int
    ): Call<List<Product>>
}