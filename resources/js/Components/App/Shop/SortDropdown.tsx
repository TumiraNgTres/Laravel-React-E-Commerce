import React from "react";

interface SortDropdownProps {
  selectedSort: string;
  onSortChange: (value: string) => void;
  disabled: boolean;
}

export default function SortDropdown({
  selectedSort,
  onSortChange,
  disabled,
}: SortDropdownProps) {
  return (
    <select
      value={selectedSort}
      onChange={(e) => onSortChange(e.target.value)}
      className="p-2 rounded border text-sm"
      disabled={disabled}
    >
      <option value="">Sort By</option>
      <option value="price_asc">Price Low to High</option>
      <option value="price_desc">Price High to Low</option>
      <option value="latest">Latest</option>
    </select>
  );
}
