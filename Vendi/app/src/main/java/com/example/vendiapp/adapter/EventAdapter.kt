package com.example.vendiapp.adapter

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.example.vendiapp.R
import com.example.vendiapp.model.EventModel

class EventAdapter(
    private var itemList: List<EventModel>,
    private val onItemClick: (EventModel) -> Unit // Click listener
) : RecyclerView.Adapter<RecyclerView.ViewHolder>() {

    companion object {
        private const val VIEW_TYPE_REGULAR = 0
        private const val VIEW_TYPE_FEATURED = 1
    }

    override fun getItemViewType(position: Int): Int {
        return if (itemList[position].isFeatured) VIEW_TYPE_FEATURED else VIEW_TYPE_REGULAR
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): RecyclerView.ViewHolder {
        val inflater = LayoutInflater.from(parent.context)
        return if (viewType == VIEW_TYPE_FEATURED) {
            val view = inflater.inflate(R.layout.item_featured_event, parent, false)
            FeaturedItemViewHolder(view, onItemClick)
        } else {
            val view = inflater.inflate(R.layout.item_event, parent, false)
            CategoryViewHolder(view, onItemClick)
        }
    }

    override fun onBindViewHolder(holder: RecyclerView.ViewHolder, position: Int) {
        val item = itemList[position]
        when (holder) {
            is FeaturedItemViewHolder -> holder.bind(item)
            is CategoryViewHolder -> holder.bind(item)
        }
    }

    override fun getItemCount() = itemList.size

    fun updateEvents(newList: List<EventModel>) {
        itemList = newList
        notifyDataSetChanged()
    }

    class CategoryViewHolder(itemView: View, private val onItemClick: (EventModel) -> Unit) :
        RecyclerView.ViewHolder(itemView) {

        private val itemImage: ImageView = itemView.findViewById(R.id.ivEventImage)
        private val itemName: TextView = itemView.findViewById(R.id.tvEventName)
        private val location: TextView = itemView.findViewById(R.id.tvLocation)
        private val price: TextView = itemView.findViewById(R.id.tvPrice)
        private val rating: TextView = itemView.findViewById(R.id.tvRating)

        fun bind(item: EventModel) {
            itemName.text = item.title
            location.text = item.location
            price.text = item.price
            rating.text = item.rating.toString()

            // Load image from URL using Glide
            Glide.with(itemView.context)
                .load(item.imageUrl)
                .placeholder(R.drawable.baseline_cloud_download_24) // Optional placeholder
                .error(R.drawable.error_image) // Optional error image
                .into(itemImage)

            itemView.setOnClickListener { onItemClick(item) }
        }
    }

    class FeaturedItemViewHolder(itemView: View, private val onItemClick: (EventModel) -> Unit) :
        RecyclerView.ViewHolder(itemView) {

        private val itemImage: ImageView = itemView.findViewById(R.id.ivFeatureEventImage)
        private val itemName: TextView = itemView.findViewById(R.id.tvFeatureEventName)
        private val location: TextView = itemView.findViewById(R.id.tvFeatureEventLocation)
        private val price: TextView = itemView.findViewById(R.id.tvFeatureEventPrice)
        private val rating: TextView = itemView.findViewById(R.id.tvFeatureEventRating)
        private val starIcon: ImageView = itemView.findViewById(R.id.ivFeatureStar)

        fun bind(item: EventModel) {
            itemName.text = item.title
            location.text = item.location
            price.text = item.price
            rating.text = item.rating.toString()

            // Load image from URL using Glide
            Glide.with(itemView.context)
                .load(item.imageUrl)
                .placeholder(R.drawable.baseline_cloud_download_24) // Optional placeholder
                .error(R.drawable.error_image) // Optional error image
                .into(itemImage)

            itemView.setOnClickListener { onItemClick(item) }
        }
    }
}
