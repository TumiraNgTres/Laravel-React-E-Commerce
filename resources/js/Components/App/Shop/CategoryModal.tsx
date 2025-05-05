import React from "react";
import { FaSearch, FaTimes } from "react-icons/fa";

interface CategoryModalProps {
  show: boolean;
  onClose: () => void;
  categories: any[];
  selectedCategory: string | null;
  setSelectedCategory: (category: string | null) => void;
  searchTerm: string;
  setSearchTerm: (term: string) => void;
}

export default function CategoryModal({
  show,
  onClose,
  categories,
  selectedCategory,
  setSelectedCategory,
  searchTerm,
  setSearchTerm,
}: CategoryModalProps) {

  const filteredCategories = categories?.filter((category) =>
    category.name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  if (!show) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg w-full max-w-md max-h-[80vh] flex flex-col">

        <div className="p-4 border-b">

          <div className="flex justify-between items-center">
            <h3 className="text-lg font-semibold">Select Category</h3>
            <button onClick={onClose} className="text-gray-500">
              <FaTimes />
            </button>
          </div>

          <div className="relative mt-2">
            <input
              type="text"
              placeholder="Search categories..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full p-2 pl-8 border rounded text-sm"
            />
            <FaSearch className="absolute left-2 top-3 text-gray-400" />
          </div>

        </div>

        <div className="overflow-y-auto flex-1">
          <ul className="divide-y">
            {filteredCategories?.map((category) => (
              <li key={category.id}>
                <button
                  className={`w-full text-left p-3 text-sm ${
                    selectedCategory === category.id
                      ? "bg-blue-50 text-primary font-medium"
                      : "text-gray-700"
                  }`}
                  onClick={() => {
                    setSelectedCategory(category.id);
                    onClose();
                  }}
                >
                  {category.name} ({category.products_count})
                </button>
              </li>
            ))}
          </ul>
        </div>

        <div className="p-4 border-t">
          <button
            onClick={() => {
              setSelectedCategory(null);
              onClose();
            }}
            className="w-full py-2 text-sm text-blue-500"
          >
            Clear Selection
          </button>
        </div>
        
      </div>
    </div>
  );
}
