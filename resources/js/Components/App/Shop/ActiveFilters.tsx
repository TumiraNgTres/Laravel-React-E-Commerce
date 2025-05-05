import React from "react";
import { FaTimes } from "react-icons/fa";

interface ActiveFiltersProps {
  isFiltering: boolean;
  selectedCategory: string | null;
  filters: any;
  onClearCategory: () => void;
}

export default function ActiveFilters({
  isFiltering,
  selectedCategory,
  filters,
  onClearCategory,
}: ActiveFiltersProps) {
  return (
    <div className="flex items-center space-x-2">
      {isFiltering && (
        <div className="text-sm text-gray-500">Applying filters...</div>
      )}

      {selectedCategory && (
        <div className="flex items-center bg-gray-100 rounded px-2 py-1 text-sm">
          Category:{" "}
          {
            filters.categories?.find((c: any) => c.id === selectedCategory)
              ?.name
          }
          <button
            onClick={onClearCategory}
            className="ml-1 text-gray-500 hover:text-gray-700"
          >
            <FaTimes size={12} />
          </button>
        </div>
      )}
    </div>
  );
}
