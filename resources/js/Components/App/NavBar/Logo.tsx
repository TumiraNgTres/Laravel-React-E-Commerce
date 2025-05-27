import React from "react";
import { Link } from "@inertiajs/react";

export default function Logo() {
  return (
    <Link href="/" className="flex-shrink-0">
      <img
        src="/images/logo-karthive-removebg.jpg"
        alt="Karthive"
        className="h-8 w-auto"
      />
    </Link>
  );
}
