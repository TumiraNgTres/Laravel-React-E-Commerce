import React from "react";

export default function Hero() {
  return (
    <div
      className="relative w-full h-64 bg-cover bg-center"
      style={{ backgroundImage: "url('/images/shop-hero.jpg')" }}
    >
      <div className="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <h1 className="text-white text-3xl font-bold">Welcome to Our Shop</h1>
      </div>
    </div>
  );
}
