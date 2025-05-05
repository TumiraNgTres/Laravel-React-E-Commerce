export const arraysAreEqual = (arr1: any[], arr2: any[]) => {
  if (arr1.length !== arr2.length) return false;

  // check if the elements on the same index are equal in both arrays - then the arrays are equal
  return arr1.every((value, index) => value === arr2[index]);
};
