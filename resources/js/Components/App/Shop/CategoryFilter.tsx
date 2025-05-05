import React from "react";
import { FaTimes } from "react-icons/fa";

interface CategoryFilterProps {
  categories: any[];
  selectedCategory: string | null;
  setSelectedCategory: (category: string | null) => void;
  setShowCategoryModal: (show: boolean) => void;
}

export default function CategoryFilter({
  categories,
  selectedCategory,
  setSelectedCategory,
  setShowCategoryModal,
}: CategoryFilterProps) {
  const displayedCategories = categories?.slice(0, 5);

  return (
    <div className="mb-6">

      <div className="flex justify-between items-center mb-3">
        <h2 className="text-md font-semibold">Categories</h2>

        {categories?.length > 5 && (
          <button
            onClick={() => setShowCategoryModal(true)}
            className="text-sm text-blue-500"
          >
            Browse All
          </button>
        )}

      </div>

      <ul className="space-y-1">
        {displayedCategories?.map((category) => (
          <li key={category.id}>
            <button
              className={`w-full text-left text-sm flex justify-between items-center p-1 ${
                selectedCategory === category.id
                  ? "font-semibold text-primary"
                  : "text-gray-700"
              }`}
              onClick={() =>
                setSelectedCategory(
                  selectedCategory === category.id ? null : category.id
                )
              }
            >
              <span>
                {category.name} ({category.products_count})
              </span>
            </button>
          </li>
        ))}
      </ul>
      {selectedCategory && (
        <div className="mt-3 pt-2 border-t border-gray-100">
          <button
            onClick={() => setSelectedCategory(null)}
            className="w-full text-left text-sm text-blue-500 font-medium p-1 hover:bg-gray-50 rounded"
          >
            Show All Products
          </button>
        </div>
      )}
    </div>
  );
}
