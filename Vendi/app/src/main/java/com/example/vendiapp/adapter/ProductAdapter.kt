package com.example.vendiapp.adapter

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.example.vendiapp.R
import com.example.vendiapp.model.Product

class ProductAdapter(private val context: Context, private var productList: MutableList<Product>) :
    RecyclerView.Adapter<ProductAdapter.ProductViewHolder>() {

    inner class ProductViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        val ivProductImage: ImageView = itemView.findViewById(R.id.ivProductImage)
        val tvProductName: TextView = itemView.findViewById(R.id.tvProductName)
        val tvProductPrice: TextView = itemView.findViewById(R.id.tvProductPrice)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ProductViewHolder {
        val view = LayoutInflater.from(context).inflate(R.layout.item_product, parent, false)
        return ProductViewHolder(view)
    }

    override fun onBindViewHolder(holder: ProductViewHolder, position: Int) {
        val product = productList[position]
        holder.tvProductName.text = product.productName
        holder.tvProductPrice.text = "₱${product.price}"

        // Load the product image using Glide
        Glide.with(context)
            .load(product.productImage)
            .placeholder(R.drawable.placeholder)
            .into(holder.ivProductImage)
    }

    override fun getItemCount(): Int {
        return productList.size
    }

    fun addProducts(newProducts: List<Product>) {
        productList.addAll(newProducts)
        notifyDataSetChanged()
    }
}
