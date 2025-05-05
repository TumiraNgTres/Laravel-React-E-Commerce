import React from "react";
import { Link } from "@inertiajs/react";
import { PaginationLink } from "@/types";

export default function Pagination({ links }: { links: PaginationLink[] }) {
  return (
    <div className="flex gap-2 mt-6">
      {links.map((link, index) => (
        <a
          key={index}
          href={link.url ?? "#"}
          dangerouslySetInnerHTML={{ __html: link.label }}
          className={`px-4 py-2 rounded ${
            link.active ? "bg-blue-500 text-white" : "bg-gray-100"
          }`}
        />
      ))}
    </div>
  );
}
