package com.example.vendiapp.api

import com.example.vendiapp.model.EventModel
import retrofit2.Call
import retrofit2.http.GET
import retrofit2.http.Query

interface EventApiService {

    @GET("events")
    fun getEvents(
        @Query("category") category: String
    ): Call<List<EventModel>>
}
