import React, { useState } from "react";
import ResponsiveNavLink from "../Core/ResponsiveNavLink";
import { Link, usePage } from "@inertiajs/react";
import MiniCartDropDown from "./MiniCartDropDown";

function NavBar() {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const { auth } = usePage().props; // get auth from inertia props
  const { user } = auth; // get user from auth

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
                  <Link href="/shop" className="font-medium">
                    Shop
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
              <Link href="/shop" className="font-medium">
                Shop
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
          <MiniCartDropDown />

          {/* user logged in */}
          {/* User profile dropdown */}
          {user && (
            <div className="dropdown dropdown-end">
              <div
                tabIndex={0}
                role="button"
                className="btn btn-ghost btn-circle avatar"
              >
                <div className="w-7 rounded-full ring ring-purple-800 ring-offset-base-100 ring-offset-2">
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
          )}

          {/* user not logged in */}
          {!user && (
            <>
              <div className="flex gap-2">
                <Link
                  href={route("login")}
                  className="btn btn-ghost btn-sm rounded-lg hover:bg-purple-800 hover:text-white transition-colors"
                >
                  Log in
                </Link>
                <Link
                  href={route("register")}
                  className="btn btn-sm rounded-lg bg-purple-800 text-white hover:bg-purple-700 focus:bg-purple-700"
                >
                  Register
                </Link>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
}

export default NavBar;
