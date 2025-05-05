import React from "react";
import ActiveFilters from "./ActiveFilters";
import SortDropdown from "./SortDropdown";

interface FilterToolbarProps {
  isFiltering: boolean;
  selectedCategory: string | null;
  filters: any;
  onClearCategory: () => void;
  selectedSort: string;
  onSortChange: (value: string) => void;
}

export default function FilterToolbar({
  isFiltering,
  selectedCategory,
  filters,
  onClearCategory,
  selectedSort,
  onSortChange,
}: FilterToolbarProps) {
  return (
    <div className="flex justify-between items-center mb-4 bg-white p-3 rounded shadow">
      <ActiveFilters
        isFiltering={isFiltering}
        selectedCategory={selectedCategory}
        filters={filters}
        onClearCategory={onClearCategory}
      />
      <SortDropdown
        selectedSort={selectedSort}
        onSortChange={onSortChange}
        disabled={isFiltering}
      />
    </div>
  );
}
