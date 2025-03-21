package com.example.vendiapp.repository

import com.example.vendiapp.model.Product
import com.example.vendiapp.network.ApiService
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

class ProductRepository {

    private val apiService: ApiService

    init {
        val retrofit = Retrofit.Builder()
            .baseUrl("http://yourserver.com/api/")
            .addConverterFactory(GsonConverterFactory.create())
            .build()

        apiService = retrofit.create(ApiService::class.java)
    }

    fun getProductsByCategory(categoryId: Int, limit: Int) = apiService.getProductsByCategory(categoryId, limit)
}