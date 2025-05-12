import React, { useState } from "react";
import { Product } from "@/types";
import { Link, useForm } from "@inertiajs/react";
import { FaCartPlus } from "react-icons/fa";
import { AiOutlineHeart, AiFillHeart } from "react-icons/ai";
import { Eye } from "lucide-react";
import CurrencyFormatter from "../Core/CurrencyFormatter";

function ProductCard({ product }: { product: Product }) {
  const [wishlisted, setWishlisted] = useState(false);

  const toggleWishlist = (e: React.MouseEvent<HTMLButtonElement>) => {
    e.preventDefault();
    e.stopPropagation();
    setWishlisted(!wishlisted);
    // Future: Integrate API call to update wishlist status
  };

  const form = useForm<{
    option_ids: Record<string, number>;
    quantity: number;
  }>({
    option_ids: {},
    quantity: 1,
  });

  const addToCart = () => {
    form.post(route("cart.store", product.id), {
      preserveScroll: true,
      preserveState: true,
      onError: (err) => {
        console.log(err);
      },
    });
  };

  return (
    <div className="card bg-white shadow-md hover:shadow-xl transition-transform duration-300 transform hover:scale-105 rounded-lg overflow-hidden">
      <figure className="relative group">
        <Link href={route("product.show", product.slug)}>
          <img
            src={product.image}
            alt={product.title}
            className="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110"
          />
        </Link>
        {/* Action Icons at Bottom Center on Hover */}
        <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
          {/* Add to Cart */}
          <button
            className="bg-white text-gray-700 rounded-full p-2 shadow-md hover:bg-gradient-to-r hover:from-indigo-500 hover:to-blue-500 hover:text-white transition-colors duration-200"
            title="Add to Cart"
            onClick={addToCart}
          >
            <FaCartPlus className="w-4 h-4" />
          </button>

          {/* Wishlist */}
          <button
            onClick={toggleWishlist}
            className="bg-white text-gray-700 rounded-full p-2 shadow-md hover:bg-gradient-to-r hover:from-pink-500 hover:to-rose-500 hover:text-white transition-colors duration-200"
            title="Add to Wishlist"
          >
            {wishlisted ? (
              <AiFillHeart className="w-4 h-4 text-red-500" />
            ) : (
              <AiOutlineHeart className="w-4 h-4" />
            )}
          </button>

          {/* View Product */}
          <Link
            href={route("product.show", product.slug)}
            className="bg-white text-gray-700 rounded-full p-2 shadow-md hover:bg-gradient-to-r hover:from-cyan-500 hover:to-teal-500 hover:text-white transition-colors duration-200"
            title="View Product"
            onClick={(e) => e.stopPropagation()}
          >
            <Eye className="w-4 h-4" />
          </Link>
        </div>
      </figure>
      <div className="p-4 text-center">
        <h2 className="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors">
          <Link href={route("product.show", product.slug)}>
            {product.title}
          </Link>
        </h2>

        {/* Department > Category Display */}
        <div className="mt-2 text-sm text-gray-500">
          in{" "}
          <Link
            href="/"
            className="font-semibold text-indigo-600 hover:underline"
          >
            {product.department.name}
          </Link>{" "}
          &gt;{" "}
          <Link
            href="/"
            className="font-semibold text-cyan-600 hover:underline"
          >
            {product.category.name}
          </Link>
        </div>

        {/* Optional: Tag Badges */}
        <div className="flex justify-center gap-2 mt-2 flex-wrap">
          <span className="bg-indigo-100 text-indigo-700 text-xs font-medium px-3 py-1 rounded-full">
            Dept: {product.department.name}
          </span>
          <span className="bg-cyan-100 text-cyan-700 text-xs font-medium px-3 py-1 rounded-full">
            Category: {product.category.name}
          </span>
        </div>

        <p className="text-sm text-gray-600 mt-2 line-clamp-3">
          {product.description ||
            "Experience quality and style with this amazing product that delivers exceptional performance with modern design elements."}
        </p>
        <div className="mt-4">
          <span className="text-lg font-bold text-indigo-600">
            <CurrencyFormatter amount={product.price} />
          </span>
        </div>
      </div>
    </div>
  );
}

export default ProductCard;
