package com.example.vendiapp.adapter

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.example.vendiapp.R
import com.example.vendiapp.model.VendorModel

class EventAdapter(
    private var itemList: List<VendorModel> = emptyList(),
    private val onItemClick: (VendorModel) -> Unit
) : RecyclerView.Adapter<EventAdapter.BaseViewHolder>() {

    companion object {
        private const val VIEW_TYPE_REGULAR = 0
        private const val VIEW_TYPE_FEATURED = 1
    }

    override fun getItemViewType(position: Int): Int {
        return if (itemList[position].is_featured) VIEW_TYPE_FEATURED else VIEW_TYPE_REGULAR
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder {
        return when (viewType) {
            VIEW_TYPE_FEATURED -> {
                val view = LayoutInflater.from(parent.context)
                    .inflate(R.layout.item_featured_event, parent, false)
                FeaturedItemViewHolder(view, onItemClick)
            }
            else -> {
                val view = LayoutInflater.from(parent.context)
                    .inflate(R.layout.item_event, parent, false)
                RegularItemViewHolder(view, onItemClick)
            }
        }
    }

    override fun onBindViewHolder(holder: BaseViewHolder, position: Int) {
        holder.bind(itemList[position])
    }

    override fun getItemCount() = itemList.size

    fun updateEvents(newList: List<VendorModel>) {
        val diffResult = DiffUtil.calculateDiff(VendorDiffCallback(itemList, newList))
        itemList = newList
        diffResult.dispatchUpdatesTo(this)
    }

    abstract class BaseViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        abstract fun bind(vendor: VendorModel)
    }

    class RegularItemViewHolder(
        itemView: View,
        private val onClick: (VendorModel) -> Unit
    ) : BaseViewHolder(itemView) {
        private val name: TextView = itemView.findViewById(R.id.tvEventName)
        private val location: TextView = itemView.findViewById(R.id.tvLocation)
        private val price: TextView = itemView.findViewById(R.id.tvPrice)
        private val rating: TextView = itemView.findViewById(R.id.tvRating)
        private val image: ImageView = itemView.findViewById(R.id.ivEventImage)

        override fun bind(vendor: VendorModel) {
            name.text = vendor.business_name
            location.text = vendor.address
            price.text = vendor.lowest_price
            rating.text = vendor.rating.toString()

            // Load image if available
            vendor.profile_picture?.let { imageUrl ->
                Glide.with(itemView.context)
                    .load(imageUrl)
                    .placeholder(R.drawable.img_bubble_tea)
                    .into(image)
            }

            itemView.setOnClickListener { onClick(vendor) }
        }
    }

    class FeaturedItemViewHolder(
        itemView: View,
        private val onClick: (VendorModel) -> Unit
    ) : BaseViewHolder(itemView) {
        private val name: TextView = itemView.findViewById(R.id.tvFeatureEventName)
        private val location: TextView = itemView.findViewById(R.id.tvFeatureEventLocation)
        private val price: TextView = itemView.findViewById(R.id.tvFeatureEventPrice)
        private val rating: TextView = itemView.findViewById(R.id.tvFeatureEventRating)
        private val image: ImageView = itemView.findViewById(R.id.ivFeatureEventImage)
        private val starIcon: ImageView = itemView.findViewById(R.id.ivFeatureStar)

        override fun bind(vendor: VendorModel) {
            name.text = vendor.business_name
            location.text = vendor.address
            price.text = vendor.lowest_price
            rating.text = vendor.rating.toString()
            starIcon.visibility = if (vendor.is_featured) View.VISIBLE else View.GONE

            // Load image if available
            vendor.profile_picture?.let { imageUrl ->
                Glide.with(itemView.context)
                    .load(imageUrl)
                    .placeholder(R.drawable.img_cocktail_mixology)
                    .into(image)
            }

            itemView.setOnClickListener { onClick(vendor) }
        }
    }

    private class VendorDiffCallback(
        private val oldList: List<VendorModel>,
        private val newList: List<VendorModel>
    ) : DiffUtil.Callback() {
        override fun getOldListSize() = oldList.size
        override fun getNewListSize() = newList.size

        override fun areItemsTheSame(oldPos: Int, newPos: Int) =
            oldList[oldPos].vendor_id == newList[newPos].vendor_id

        override fun areContentsTheSame(oldPos: Int, newPos: Int) =
            oldList[oldPos] == newList[newPos]
    }
}