// File: resources/js/Pages/Shop.tsx

import React from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PageProps, PaginationProps, Product } from "@/types";
import { Head } from "@inertiajs/react";
import ProductItem from "@/Components/App/ProductItem";

export default function Shop({
  products,
}: PageProps<{
  products: PaginationProps<Product>;
}>) {
  return (
    <AuthenticatedLayout>
      <Head title="Shop" />

      {/* Hero Banner */}
      <div
        className="relative w-full h-96 bg-cover bg-center"
        style={{ backgroundImage: "url('/images/shop-hero.jpg')" }}
      >
        <div className="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
          <h1 className="text-white text-4xl font-bold">Welcome to Our Shop</h1>
        </div>
      </div>

      {/* Product Grid */}
      <div className="p-8">
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          {products.data.map((product) => (
            <ProductItem product={product} key={product.id} />
          ))}
        </div>
        {/* Future: Implement pagination controls here */}
      </div>
    </AuthenticatedLayout>
  );
}
