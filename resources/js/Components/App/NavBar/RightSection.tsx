import React from "react";
import SearchBar from "./SearchBar";
import MobileSearch from "./MobileSearch";
import AuthSection from "./AuthSection";
import MiniCartDropDown from "../MiniCartDropDown";

type RightSectionProps = {
  user: any;
};

export default function RightSection({ user }: RightSectionProps) {
  return (
    <div className="flex items-center space-x-3">
      <SearchBar />
      <MobileSearch />
      <MiniCartDropDown />
      <AuthSection user={user} />
    </div>
  );
}
