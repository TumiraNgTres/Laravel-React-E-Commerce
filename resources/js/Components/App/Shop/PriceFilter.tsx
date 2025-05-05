import React from "react";

interface PriceFilterProps {
  priceRange: [number, number];
  handlePriceChange: (
    e: React.ChangeEvent<HTMLInputElement>,
    index: number
  ) => void;
}

export default function PriceFilter({
  priceRange,
  handlePriceChange,
}: PriceFilterProps) {
  return (
    <div className="mb-6">
      <h2 className="text-md font-semibold mb-3">Price Range</h2>
      <div className="space-y-2">
        <div className="flex items-center gap-2">
          <input
            type="number"
            min="0"
            max="10000"
            value={priceRange[0]}
            onChange={(e) => handlePriceChange(e, 0)}
            className="w-full p-2 border rounded text-sm"
            placeholder="Min"
          />
          <span className="text-gray-500">to</span>
          <input
            type="number"
            min="0"
            max="10000"
            value={priceRange[1]}
            onChange={(e) => handlePriceChange(e, 1)}
            className="w-full p-2 border rounded text-sm"
            placeholder="Max"
          />
        </div>
      </div>
    </div>
  );
}
