package com.example.vendiapp

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView

class PackageAdapter(
    private var packageList: List<PackageModel>
) : RecyclerView.Adapter<PackageAdapter.PackageViewHolder>() {

    inner class PackageViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val tvPackageName: TextView = view.findViewById(R.id.tvPackageName)
        val tvPackageSize: TextView = view.findViewById(R.id.tvPackageSize)
        val tvPackagePrice: TextView = view.findViewById(R.id.tvPackagePrice)
        val ivPackageImage: ImageView = view.findViewById(R.id.ivPackageImage)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): PackageViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_package, parent, false)
        return PackageViewHolder(view)
    }

    override fun onBindViewHolder(holder: PackageViewHolder, position: Int) {
        val packageModel = packageList[position]
        holder.tvPackageName.text = packageModel.packageName
        holder.tvPackagePrice.text = packageModel.price
        holder.tvPackageSize.text = packageModel.packageSize
        holder.ivPackageImage.setImageResource(packageModel.imageResource)
    }

    override fun getItemCount(): Int = packageList.size

    // ✅ Add this function to update package list dynamically
    fun updatePackages(newPackages: List<PackageModel>) {
        packageList = newPackages
        notifyDataSetChanged() // Refresh RecyclerView
    }
}