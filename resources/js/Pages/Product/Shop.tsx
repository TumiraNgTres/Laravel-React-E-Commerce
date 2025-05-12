import React, { useEffect, useState, useCallback } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PaginatedProducts } from "@/types";
import { Head, router } from "@inertiajs/react";
import ProductItem from "@/Components/App/ProductItem";
import Pagination from "@/Components/Core/Pagination";
import { FaTimes } from "react-icons/fa";
import { debounce } from "lodash";
import Hero from "@/Components/App/Hero";
import CategoryFilter from "@/Components/App/Shop/CategoryFilter";
import PriceFilter from "@/Components/App/Shop/PriceFilter";
import CategoryModal from "@/Components/App/Shop/CategoryModal";
import FilterToolbar from "@/Components/App/Shop/FilterToolbar";
interface ShopPageProps {
  products: PaginatedProducts;
  filters: any;
}

export default function Shop({ products, filters }: ShopPageProps) {
  const [selectedCategory, setSelectedCategory] = useState<string | null>(null);
  const [selectedSort, setSelectedSort] = useState<string>("");
  const [priceRange, setPriceRange] = useState<[number, number]>([0, 10000]);
  const [showCategoryModal, setShowCategoryModal] = useState(false);
  const [categorySearch, setCategorySearch] = useState("");
  const [isFiltering, setIsFiltering] = useState(false);

  // debounce and callback for only make search request after 600 seconds delay like livewire debounce

  // useCallback - memoizes (memorize) the function. so that on each rendre it dont create a new function
  const applyFilters = useCallback(
    debounce((params: any) => {
      router.get(route("shop"), params, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => setIsFiltering(false),
      });
    }, 600),
    []
  );

  useEffect(() => {
    setIsFiltering(true);

    applyFilters({
      category: selectedCategory,
      sort: selectedSort,
      price_min: priceRange[0],
      price_max: priceRange[1],
    });
  }, [selectedCategory, selectedSort, priceRange, applyFilters]);

  const handlePriceChange = (
    e: React.ChangeEvent<HTMLInputElement>,
    index: number
  ) => {
    const newValue = [...priceRange] as [number, number];
    const value = parseInt(e.target.value) || 0;
    newValue[index] = Math.min(Math.max(value, 0), 10000);
    setPriceRange(newValue);
  };

  return (
    <AuthenticatedLayout>
      <Head title="Shop" />

      <Hero />

      <div className="flex flex-col md:flex-row px-4 py-6 gap-6 bg-gray-50">
        {/* Left Filter Sidebar */}
        <div className="w-full md:w-1/5 bg-white p-4 rounded shadow">
          <CategoryFilter
            categories={filters.categories}
            selectedCategory={selectedCategory}
            setSelectedCategory={setSelectedCategory}
            setShowCategoryModal={setShowCategoryModal}
          />

          <PriceFilter
            priceRange={priceRange}
            handlePriceChange={handlePriceChange}
          />
        </div>
        {/* Right: Product Grid and Sort */}
        <div className="w-full md:w-4/5">
          {/* filter bar  */}
          <FilterToolbar
            isFiltering={isFiltering}
            selectedCategory={selectedCategory}
            filters={filters}
            onClearCategory={() => setSelectedCategory(null)}
            selectedSort={selectedSort}
            onSortChange={(value) => setSelectedSort(value)}
          />

          {/* list products */}
          {products.data.length > 0 ? (
            <>
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                {products.data.map((product) => (
                  <ProductItem product={product} key={product.id} />
                ))}
              </div>
              <Pagination
                links={products.links}
                currentPage={products.meta.current_page}
                lastPage={products.meta.last_page}
              />
            </>
          ) : (
            <div className="bg-white p-8 text-center rounded shadow">
              <p className="text-gray-500">
                No products found matching your filters.
              </p>
              <button
                onClick={() => {
                  setSelectedCategory(null);
                  setSelectedSort("");
                  setPriceRange([0, 10000]);
                }}
                className="mt-4 text-primary text-sm font-medium"
              >
                Clear all filters
              </button>
            </div>
          )}
        </div>
      </div>

      <CategoryModal
        show={showCategoryModal}
        onClose={() => setShowCategoryModal(false)}
        categories={filters.categories}
        selectedCategory={selectedCategory}
        setSelectedCategory={setSelectedCategory}
        searchTerm={categorySearch}
        setSearchTerm={setCategorySearch}
      />
    </AuthenticatedLayout>
  );
}
