import { Config } from "ziggy-js";

export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at?: string;
  stripe_account_active: boolean;
  vendor: {
    status: string;
    status_label: string;
    store_name: string;
    store_address: string;
    cover_image: string;
  };
}

export interface Vendor {
  id: number;
  store_name: string;
  store_address: string;
}

// ------------ PRODUCT ---------------
export type Image = {
  id: number;
  thumb: string;
  small: string;
  large: string;
};

export type VariationTypeOption = {
  id: number;
  name: string;
  images: Image[];
  type: VariationType;
};

export type VariationType = {
  id: number;
  name: string;
  type: "Select" | "Radio" | "Image";
  options: VariationTypeOption[];
};

export type Product = {
  id: number;
  title: string;
  slug: string;
  price: number;
  quantity: number;
  image: string;
  images: Image[];
  description: string;
  short_description: string;
  user: {
    id: number;
    name: string;
    store_name: string;
  };
  department: {
    id: number;
    name: string;
    slug: string;
  };
  category: {
    id: number;
    name: string;
  };
  variationTypes: VariationType[];
  variations: Array<{
    id: number;
    variation_type_option_ids: number[];
    quantity: number;
    price: number;
  }>;
};

// -------------------------------------------
// -------- DEPARTMENTS AND CATEGORIES--------
export interface Category {
  id: number;
  name: string;
}
export interface Department {
  id: number;
  name: string;
  slug: string;
  meta_title: string;
  meta_description: string;
  categories: Category[];
}

// -------------------------------------------

export interface PaginatedProducts {
  data: Product[];
  links: any;
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export interface ProductPaginationProps {
  links: PaginationLink[]; // Simplified from nested structure
  currentPage: number;
  lastPage: number;
}

export interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

// ---------------------------------------
// -------------- CART -------------------
export interface cartItems {
  id: number;
  product_id: number;
  title: string;
  slug: string;
  price: number;
  quantity: number;
  option_ids: Record<string, number>;
  options: VariationTypeOption[];
  image: string;
}
export interface GroupedCartItems {
  user: User;
  items: cartItems[];
  totalPrice: number;
  totalQuantity: number;
}
// ---------------------------------------
// -------------- ORDER ------------------
export interface OrderItem {
  id: number;
  quantity: number;
  price: number;
  variation_type_option_ids: number[];
  product: {
    id: number;
    title: string;
    slug: string;
    description: string;
    image: Image;
  };
}

export interface Order {
  id: number;
  total_price: number;
  status: string;
  created_at: string;
  vendorUser: {
    id: number;
    name: string;
    store_name: string;
    store_address: string;
  };
  orderItems: OrderItem[];
}
// ---------------------------------------

export type PaginationProps<T> = {
  data: Array<T>;
};

export type PageProps<
  T extends Record<string, unknown> = Record<string, unknown>
> = T & {
  appName: string;
  csrf_token: string;
  auth: {
    user: User;
  };
  ziggy: Config & { location: string };
  success: {
    message: string;
    time: number;
  };
  error: string;
  totalQuantity: number;
  totalPrice: number;
  miniCartItems: cartItems[];
  departments: Department[];
};
