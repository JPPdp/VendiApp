package com.example.vendiapp.repository

import android.util.Log
import com.example.vendiapp.api.RetrofitClient
import com.example.vendiapp.model.VendorModel
import com.example.vendiapp.model.VendorResponse
import retrofit2.HttpException
import retrofit2.Response
import java.io.IOException

class EventRepository {
    private val vendorApiService = RetrofitClient.vendorApiService

    suspend fun getVendorsFromApi(category: String): List<VendorModel> {
        return try {
            Log.d("EventRepository", "Fetching vendors for category: $category")

            // Make the API call and get the Response object
            val response: Response<VendorResponse> = vendorApiService.getVendorsByCategory(category)

            // Check if the response was successful (HTTP 200-299)
            if (response.isSuccessful) {
                val body: VendorResponse? = response.body()

                if (body?.status == "success") {
                    body.vendors?.let {
                        Log.d("EventRepository", "Fetched ${it.size} vendors")
                        return it
                    } ?: run {
                        Log.e("EventRepository", "Vendors list was null")
                        emptyList()
                    }
                } else {
                    Log.e("EventRepository", "API returned error status: ${body?.message}")
                    emptyList()
                }
            } else {
                Log.e("EventRepository",
                    "API call failed with code: ${response.code()}, message: ${response.errorBody()?.string()}")
                emptyList()
            }
        } catch (e: HttpException) {
            Log.e("EventRepository", "HTTP error: ${e.message()}", e)
            emptyList()
        } catch (e: IOException) {
            Log.e("EventRepository", "Network error: ${e.message}", e)
            emptyList()
        } catch (e: Exception) {
            Log.e("EventRepository", "Unexpected error: ${e.message}", e)
            emptyList()
        }
    }
}