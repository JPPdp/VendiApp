package com.example.vendiapp

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.vendiapp.model.Event

class EventAdapter(private var itemList: List<Event>) : RecyclerView.Adapter<RecyclerView.ViewHolder>() {

    companion object {
        private const val VIEW_TYPE_REGULAR = 0
        private const val VIEW_TYPE_FEATURED = 1
    }

    // Determines the view type based on whether the item is featured or not
    override fun getItemViewType(position: Int): Int {
        return if (itemList[position].isFeatured) VIEW_TYPE_FEATURED else VIEW_TYPE_REGULAR
    }

    // Inflates the appropriate layout based on view type
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): RecyclerView.ViewHolder {
        val inflater = LayoutInflater.from(parent.context)
        return if (viewType == VIEW_TYPE_FEATURED) {
            val view = inflater.inflate(R.layout.item_featured_event, parent, false)
            FeaturedItemViewHolder(view)
        } else {
            val view = inflater.inflate(R.layout.item_event, parent, false)
            CategoryViewHolder(view)
        }
    }

    // Binds data to the ViewHolder based on its type
    override fun onBindViewHolder(holder: RecyclerView.ViewHolder, position: Int) {
        val item = itemList[position]
        if (holder is FeaturedItemViewHolder) {
            holder.bind(item) // Updates featured item UI
        } else if (holder is CategoryViewHolder) {
            holder.bind(item) // Updates regular item UI
        }
    }

    // Returns the total number of items
    override fun getItemCount() = itemList.size

    // Updates the dataset and notifies the adapter to refresh the UI
    fun updateData(newList: List<Event>) {
        itemList = newList
        notifyDataSetChanged() // Triggers UI update
    }

    class CategoryViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val itemImage: ImageView = itemView.findViewById(R.id.ivEventImage)
        private val itemName: TextView = itemView.findViewById(R.id.tvEventName)
        private val location: TextView = itemView.findViewById(R.id.tvLocation)
        private val price: TextView = itemView.findViewById(R.id.tvPrice)
        private val rating: TextView = itemView.findViewById(R.id.tvRating)

        // Binds the regular item data to the UI elements
        fun bind(item: Event) {
            itemName.text = item.name
            location.text = item.location
            price.text = item.price
            rating.text = item.rating.toString()
            itemImage.setImageResource(item.imageRes)
        }
    }

    class FeaturedItemViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val itemImage: ImageView = itemView.findViewById(R.id.ivFeatureEventImage)
        private val itemName: TextView = itemView.findViewById(R.id.tvFeatureEventName)
        private val price: TextView = itemView.findViewById(R.id.tvFeaturePrice)

        // Binds the featured item data to the UI elements
        fun bind(item: Event) {
            itemName.text = item.name
            price.text = item.price
            itemImage.setImageResource(item.imageRes)
        }
    }
}
