import React, { useState } from "react";
import { Product } from "@/types";
import { Link } from "@inertiajs/react";
import { Eye } from "lucide-react";
import { FaCartPlus } from "react-icons/fa6";
import { AiOutlineHeart, AiFillHeart } from "react-icons/ai";
import CurrencyFormatter from "../Core/CurrencyFormatter";

function ProductItem({ product }: { product: Product }) {
  const [wishlisted, setWishlisted] = useState(false);

  const toggleWishlist = () => {
    setWishlisted(!wishlisted);
    // Future: Add/remove wishlist via API
  };

  return (
    <div className="bg-white border rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
      <Link href={route("product.show", product.slug)}>
        <img
          src={product.image}
          alt={product.title}
          className="w-full aspect-square object-cover hover:scale-105 transition-transform duration-300"
        />
      </Link>

      <div className="p-4 space-y-2">
        <h2 className="text-md font-semibold line-clamp-2 text-gray-800">
          <Link
            href={route("product.show", product.slug)}
            className="hover:text-primary transition-colors"
          >
            {product.title}
          </Link>
        </h2>

        <p className="text-xs text-gray-500">
          by{" "}
          <Link href="/" className="hover:underline text-primary">
            {product.user.name}
          </Link>{" "}
          in{" "}
          <Link href="/" className="hover:underline text-indigo-500">
            {product.department.name}
          </Link>
        </p>

        <div className="mt-3 flex items-center justify-between">
          <span className="text-lg font-bold text-green-600">
            <CurrencyFormatter
              amount={product.price}
              currency="USD"
              locale="en-US"
            />
          </span>

          <div className="flex gap-2">
            {/* Add to Cart */}
            <button
              className="bg-white text-gray-700 rounded-full p-2 transition-all duration-200 shadow-md hover:scale-110 hover:bg-[#6D28D9] hover:text-white"
              title="Add to Cart"
            >
              <FaCartPlus className="w-5 h-5" />
            </button>

            {/* Wishlist */}
            <button
              onClick={toggleWishlist}
              className="bg-white rounded-full p-2 transition-all duration-200 shadow-md hover:scale-110 hover:bg-[#6D28D9] hover:text-white"
              title="Add to Wishlist"
            >
              {wishlisted ? (
                <AiFillHeart className="w-5 h-5 text-red-500 transition-colors duration-300" />
              ) : (
                <AiOutlineHeart className="w-5 h-5 transition-colors duration-300" />
              )}
            </button>

            {/* View Product */}
            <Link
              href={route("product.show", product.slug)}
              className="bg-white text-gray-700 rounded-full p-2 shadow-md hover:bg-[#6D28D9] hover:text-white transition-all duration-200 hover:scale-110"
              title="View Product"
            >
              <Eye className="w-5 h-5" />
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}

export default ProductItem;
