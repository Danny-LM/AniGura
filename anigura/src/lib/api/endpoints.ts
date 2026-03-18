
export const ENDPOINTS = {
  AUTH: {
    LOGIN:    "/auth/login",
    REGISTER: "/auth/register",
    REFRESH:  "/auth/refresh",
    LOGOUT:   "/auth/logout",
  },

  USERS: {
    BASE:   "/users",
    SEARCH: "/users/search",
    BY_ID:  (id: number) => `/users/${id}`,
  },

  FRANCHISES: {
    BASE:  "/franchises",
    BY_ID: (id: number) => `/franchises/${id}`,
  },

  PUBLISHERS: {
    BASE:  "/publishers",
    BY_ID: (id: number) => `/publishers/${id}`,
  },

  MEDIA_ENTRIES: {
    BASE:  "/media_entries",
    BY_ID: (id: number) => `/media_entries/${id}`,
  },

  PRODUCTS: {
    BASE:  "/products",
    BY_ID: (id: number) => `/products/${id}`,
  },

  IMAGES: {
    BASE:       "/images",
    BY_ID:      (id: number)        => `/images/${id}`,
    COVER:      (productId: number) => `/images/cover/${productId}`,
    BY_PRODUCT: (productId: number) => `/images/product/${productId}`,
  },

  CART: {
    BASE:     "/cart",
    VALIDATE: "/cart/validate",
    ITEM:     (itemId: number) => `/cart/${itemId}`,
  },

  ADDRESSES: {
    BASE:  "/addresses",
    ME:    "/addresses/me",
    BY_ID: (id: number) => `/addresses/${id}`,
  },

  ORDERS: {
    BASE:      "/orders",
    BY_ID:     (id: number) => `/orders/${id}`,
    MY_ORDERS: "/orders/me",
    MY_BY_ID:  (id: number) => `/orders/me/${id}`,
    CANCEL:    (id: number) => `/orders/me/${id}/cancel`,
  },
} as const;

