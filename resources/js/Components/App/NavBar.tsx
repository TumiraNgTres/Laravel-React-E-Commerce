import React, { useState } from "react";
import ResponsiveNavLink from "../Core/ResponsiveNavLink";
import { Link } from "@inertiajs/react";

function NavBar() {
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  return (
    <div className="shadow-sm border-b border-gray-100 dark:border-gray-700 dark:bg-gray-800 sticky top-0 z-10 bg-base-100">
      <div className="navbar container mx-auto px-4">
        {/* Mobile menu button */}
        <div className="navbar-start">
          <div className="lg:hidden dropdown">
            <button
              onClick={() => setIsMenuOpen(!isMenuOpen)}
              className="btn btn-ghost btn-circle"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth="2"
                  d="M4 6h16M4 12h16M4 18h7"
                />
              </svg>
            </button>
            {isMenuOpen && (
              <ul className="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52 absolute left-0">
                <li>
                  <Link href="/" className="font-medium">
                    Home
                  </Link>
                </li>
                <li>
                  <Link href="/products" className="font-medium">
                    Products
                  </Link>
                </li>
                <li>
                  <Link href="/about" className="font-medium">
                    About
                  </Link>
                </li>
                <li>
                  <Link href="/contact" className="font-medium">
                    Contact
                  </Link>
                </li>
              </ul>
            )}
          </div>
          <Link href="/" className="btn btn-ghost text-xl font-bold">
            myStore
          </Link>
        </div>

        {/* Desktop navigation links */}
        <div className="navbar-center hidden lg:flex">
          <ul className="menu menu-horizontal px-1">
            <li>
              <Link href="/" className="font-medium">
                Home
              </Link>
            </li>
            <li>
              <Link href="/products" className="font-medium">
                Products
              </Link>
            </li>
            <li>
              <Link href="/about" className="font-medium">
                About
              </Link>
            </li>
            <li>
              <Link href="/contact" className="font-medium">
                Contact
              </Link>
            </li>
          </ul>
        </div>

        {/* Right side icons */}
        <div className="navbar-end">
          {/* Cart dropdown */}
          <div className="dropdown dropdown-end mr-2">
            <div
              tabIndex={0}
              role="button"
              className="btn btn-ghost btn-circle"
            >
              <div className="indicator">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                  />
                </svg>
                <span className="badge badge-sm indicator-item badge-primary">
                  8
                </span>
              </div>
            </div>
            <div
              tabIndex={0}
              className="card card-compact dropdown-content bg-base-100 z-[1] mt-3 w-52 shadow"
            >
              <div className="card-body">
                <span className="text-lg font-bold">8 Items</span>
                <span className="text-info">Subtotal: $999</span>
                <div className="card-actions">
                  <button className="btn btn-primary btn-block">
                    View cart
                  </button>
                </div>
              </div>
            </div>
          </div>

          {/* User profile dropdown */}
          <div className="dropdown dropdown-end">
            <div
              tabIndex={0}
              role="button"
              className="btn btn-ghost btn-circle avatar"
            >
              <div className="w-10 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                <img
                  alt="User avatar"
                  src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp"
                />
              </div>
            </div>
            <ul
              tabIndex={0}
              className="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow"
            >
              <li>
                <ResponsiveNavLink
                  className="justify-between"
                  href={route("profile.edit")}
                >
                  Profile
                  <span className="badge badge-sm badge-primary">New</span>
                </ResponsiveNavLink>
              </li>
              <li>
                <ResponsiveNavLink className="justify-between" href="">
                  Settings
                </ResponsiveNavLink>
              </li>
              <li>
                <ResponsiveNavLink
                  method="post"
                  href={route("logout")}
                  as="button"
                >
                  Log Out
                </ResponsiveNavLink>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
}

export default NavBar;
