package com.example.vendiapp.adapter

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.example.vendiapp.R
import com.example.vendiapp.model.Product
import kotlinx.android.synthetic.main.item_product.view.*

class ProductAdapter(
    private val context: Context,
    private val products: MutableList<Product>
) : RecyclerView.Adapter<ProductAdapter.ProductViewHolder>() {

    inner class ProductViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        fun bind(product: Product) {
            itemView.tvProductName.text = product.product_name
            itemView.tvProductPrice.text = "₱ ${product.price}"

            Glide.with(context)
                .load(product.product_image) // Use URL for image
                .placeholder(R.drawable.placeholder_image)
                .into(itemView.ivProductImage)
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ProductViewHolder {
        val view = LayoutInflater.from(context).inflate(R.layout.item_product, parent, false)
        return ProductViewHolder(view)
    }

    override fun onBindViewHolder(holder: ProductViewHolder, position: Int) {
        holder.bind(products[position])
    }

    override fun getItemCount(): Int = products.size

    fun addProducts(newProducts: List<Product>) {
        products.addAll(newProducts)
        notifyDataSetChanged()
    }
}
