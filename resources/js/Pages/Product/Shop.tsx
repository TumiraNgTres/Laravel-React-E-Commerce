import React, { useEffect, useState } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PageProps, PaginationProps, Product } from "@/types";
import { Head, Link, router, usePage } from "@inertiajs/react";
import ProductItem from "@/Components/App/ProductItem";
import Pagination from "@/Components/Core/Pagination";

export default function Shop({
  products,
  filters,
}: PageProps<{ products: PaginationProps<Product>; filters: any }>) {
  const [selectedCategory, setSelectedCategory] = useState<string | null>(null);
  const [selectedSort, setSelectedSort] = useState<string>("");
  const [priceRange, setPriceRange] = useState<[number, number]>([0, 10000]);

  const applyFilters = () => {
    router.get(
      route("shop"),
      {
        category: selectedCategory,
        sort: selectedSort,
        price_min: priceRange[0],
        price_max: priceRange[1],
      },
      {
        preserveScroll: true,
        preserveState: true,
      }
    );
  };

  useEffect(() => {
    applyFilters();
  }, [selectedCategory, selectedSort, priceRange]);

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

      {/* Filters Section */}
      <div className="p-6 bg-gray-100">
        <div className="flex flex-wrap gap-4 items-center justify-between">
          {/* Category Filter */}
          <select
            value={selectedCategory || ""}
            onChange={(e) => setSelectedCategory(e.target.value)}
            className="p-2 rounded border"
          >
            <option value="">All Categories</option>
            {filters.categories?.map((category: any) => (
              <option key={category.id} value={category.id}>
                {category.name}
              </option>
            ))}
          </select>

          {/* Sort Filter */}
          <select
            value={selectedSort}
            onChange={(e) => setSelectedSort(e.target.value)}
            className="p-2 rounded border"
          >
            <option value="">Sort By</option>
            <option value="price_asc">Price Low to High</option>
            <option value="price_desc">Price High to Low</option>
            <option value="latest">Latest</option>
          </select>
        </div>
      </div>

      {/* Product Grid */}
      <div className="p-8">
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          {products.data.map((product) => (
            <ProductItem product={product} key={product.id} />
          ))}
        </div>

        {/* Pagination Controls */}
        {/* <div className="mt-8">
          <Pagination links={products.links} />
        </div> */}
      </div>
    </AuthenticatedLayout>
  );
}
