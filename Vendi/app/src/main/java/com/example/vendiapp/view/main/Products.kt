package com.example.vendiapp.view.products

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.AbsListView
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.GridLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.vendiapp.R
import com.example.vendiapp.adapter.ProductAdapter
import com.example.vendiapp.model.Product
import okhttp3.*
import org.json.JSONArray
import java.io.IOException

class ProductsFragment : Fragment() {

    private lateinit var productAdapter: ProductAdapter
    private var isLoading = false
    private var offset = 0
    private val limit = 10
    private val categoryId = 1 // Default category

    override fun onCreateView(inflater: LayoutInflater, container: ViewGroup?,
                              savedInstanceState: Bundle?): View? {
        val view = inflater.inflate(R.layout.fragment_products, container, false)

        val rvProducts: RecyclerView = view.findViewById(R.id.rvProducts)
        productAdapter = ProductAdapter(requireContext(), mutableListOf())

        rvProducts.apply {
            layoutManager = GridLayoutManager(context, 2)
            adapter = productAdapter
            addOnScrollListener(scrollListener)
        }

        loadProducts(offset)

        return view
    }

    private fun loadProducts(offset: Int) {
        isLoading = true
        val url = "http://192.168.0.100/vendiapp/api/products/get_products.php?category_id=$categoryId&limit=$limit&offset=$offset"

        val request = Request.Builder().url(url).build()
        OkHttpClient().newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                isLoading = false
            }

            override fun onResponse(call: Call, response: Response) {
                response.body?.let {
                    val productList = parseProductJson(it.string())
                    activity?.runOnUiThread {
                        productAdapter.addProducts(productList)
                        isLoading = false
                    }
                }
            }
        })
    }

    private fun parseProductJson(jsonString: String): List<Product> {
        val productList = mutableListOf<Product>()
        val jsonArray = JSONArray(jsonString)

        for (i in 0 until jsonArray.length()) {
            val jsonObject = jsonArray.getJSONObject(i)
            productList.add(
                Product(
                    jsonObject.getInt("product_id"),
                    jsonObject.getString("product_name"),
                    jsonObject.getDouble("price"),
                    jsonObject.getString("product_image")
                )
            )
        }
        return productList
    }

    private val scrollListener = object : RecyclerView.OnScrollListener() {
        override fun onScrollStateChanged(recyclerView: RecyclerView, newState: Int) {
            super.onScrollStateChanged(recyclerView, newState)
            if (!recyclerView.canScrollVertically(1) && !isLoading) {
                offset += limit
                loadProducts(offset)
            }
        }
    }
}
