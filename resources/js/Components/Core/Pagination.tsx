import React from "react";
import { Link } from "@inertiajs/react";
import { ProductPaginationProps } from "@/types";

export default function Pagination({
  links,
  currentPage,
  lastPage,
}: ProductPaginationProps) {
  if (lastPage <= 1) return null;

  return (
    <nav className="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0 mt-6">
      <div className="flex flex-1 justify-between sm:hidden">
        {currentPage > 1 && (
          <Link
            href={
              links.find((l) => l.label.toLowerCase().includes("previous"))
                ?.url || ""
            }
            preserveState
            className="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
          >
            Previous
          </Link>
        )}
        {currentPage < lastPage && (
          <Link
            href={
              links.find((l) => l.label.toLowerCase().includes("next"))?.url ||
              ""
            }
            preserveState
            className="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
          >
            Next
          </Link>
        )}
      </div>
      <div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-center">
        <div>
          <nav
            className="isolate inline-flex -space-x-px rounded-md shadow-sm"
            aria-label="Pagination"
          >
            {links.map((link, index) => {
              if (!link.url) return null;

              return (
                <Link
                  key={index}
                  href={link.url}
                  preserveState
                  className={`relative inline-flex items-center px-4 py-2 text-sm font-medium ${
                    link.active
                      ? "z-10 bg-primary text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                      : "text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0"
                  }`}
                  dangerouslySetInnerHTML={{ __html: link.label }}
                />
              );
            })}
          </nav>
        </div>
      </div>
    </nav>
  );
}
