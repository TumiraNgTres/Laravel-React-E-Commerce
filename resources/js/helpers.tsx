import { cartItems } from "./types";

export const arraysAreEqual = (arr1: any[], arr2: any[]) => {
  if (arr1.length !== arr2.length) return false;

  // check if the elements on the same index are equal in both arrays - then the arrays are equal
  return arr1.every((value, index) => value === arr2[index]);
};

// create route with the selected variation of the product for cart page same like product detail
export const ProductRoute = (item: cartItems) => {
  // get url
  const params = new URLSearchParams();
  Object.entries(item.option_ids).forEach(([typeId, optionId]) => {
    params.append(`options[${typeId}]`, optionId + "");
  });
  return route("product.show", item.slug) + "?" + params.toString();
};
