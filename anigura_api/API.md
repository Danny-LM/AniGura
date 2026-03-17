# Anigura API — Documentation
> Version 1.0.0-alpha · Author: Danny-LM

Anigura is a REST API for an anime/manga e-commerce store. It handles everything from user authentication to order management with stock control.

---

## Table of Contents

- [Base URL & Response Format](#base-url--response-format)
- [Authentication](#authentication)
- [Pagination](#pagination)
- [Idempotency](#idempotency)
- [Endpoints](#endpoints)
  - [Auth](#auth)
  - [Users](#users)
  - [Franchises](#franchises)
  - [Publishers](#publishers)
  - [Media Entries](#media-entries)
  - [Products](#products)
  - [Product Images](#product-images)
  - [Addresses](#addresses)
  - [Cart](#cart)
  - [Orders](#orders)
- [Error Reference](#error-reference)

---

## Base URL & Response Format

```
http://localhost:8000
```

Every response from the API follows this structure:

```json
{
  "status": "success | error",
  "code": 200,
  "msg": "Response message",
  "data": { ... },
  "timestamp": "2026-03-17 12:00:00"
}
```

---

## Authentication

Most endpoints require a JWT access token. Send it in the `Authorization` header:

```
Authorization: Bearer {your_access_token}
```

Access tokens expire based on `JWT_ACCESS_EXP` (default: 2 hours).
When expired, use `POST /auth/refresh` to get a new one without logging in again.

**Access levels:**
| Level | Description |
|-------|-------------|
| Public | No token needed |
| Auth | Any valid token (user or admin) |
| Admin | Token with `role: admin` required |

---

## Pagination

All list endpoints support pagination via query parameters:

| Param | Type | Default | Max | Description |
|-------|------|---------|-----|-------------|
| `page` | int | 1 | — | Page number |
| `limit` | int | 20 | 100 | Items per page |

**Example:** `GET /products?page=2&limit=5`

**Paginated response structure:**
```json
{
  "data": {
    "info": {
      "total": 50,
      "pages": 10,
      "current": 2,
      "next": 3,
      "prev": 1
    },
    "results": [ ... ]
  }
}
```

---

## Idempotency

Two critical endpoints support idempotency to prevent accidental duplicate operations:

- `POST /orders` — prevents creating the same order twice
- `PATCH /orders/me/:id/cancel` — prevents double stock refunds

To use it, send a unique key in the header:

```
X-Idempotency-Key: your-unique-uuid-here
```

The key is optional. If not sent, no idempotency protection is applied.

**Key lifecycle:**

| Status | Meaning |
|--------|---------|
| `processing` | Request is currently being executed — returns `409` if retried |
| `completed` | Request succeeded — returns the same response if retried |
| `failed` | Request failed — allows a retry with the same key |

Keys expire after **24 hours**.

---

## Endpoints

### Auth

#### POST /auth/register
Creates a new user account and returns tokens (auto-login).

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `full_name` | string | ✱ | max 255 chars |
| `email` | string | ✱ | Valid email, max 150 chars |
| `password` | string | ✱ | min 8, max 255 chars |
| `rfc` | string | ? | Mexican tax ID, max 13 chars |

**Response 201**
```json
{
  "access_token": "eyJ...",
  "refresh_token": "abc123...",
  "user": {
    "id": 1,
    "role": "user",
    "full_name": "Danny LM",
    "email": "danny@anigura.mx",
    "rfc": null
  }
}
```

**Errors**
| Code | Message |
|------|---------|
| 400 | Field validation errors |
| 409 | Email already in use |

---

#### POST /auth/login
Authenticates a user and returns tokens.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `email` | string | ✱ | Registered email |
| `password` | string | ✱ | Account password |

**Response 200**
```json
{
  "access_token": "eyJ...",
  "refresh_token": "abc123...",
  "user": {
    "id": 1,
    "role": "admin",
    "full_name": "Danny LM",
    "email": "danny@anigura.mx",
    "rfc": null
  }
}
```

**Errors**
| Code | Message |
|------|---------|
| 401 | Invalid credentials |

---

#### POST /auth/refresh
Gets a new access token using a valid refresh token.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `refresh_token` | string | ✱ | Valid, non-expired refresh token |

**Response 200**
```json
{
  "access_token": "eyJ..."
}
```

**Errors**
| Code | Message |
|------|---------|
| 401 | Invalid refresh token |
| 401 | Refresh token expired |

---

#### POST /auth/logout
🔒 Auth — Invalidates the refresh token.

**Headers**
| Header | Required |
|--------|----------|
| Authorization | ✱ |

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `refresh_token` | string | ✱ | Token to invalidate |

**Response 200**
```json
null
```

**Errors**
| Code | Message |
|------|---------|
| 401 | Authorization header missing |
| 401 | Invalid refresh token |

---

### Users

> All user endpoints require **Admin** role.

#### GET /users
🔒 Admin — Returns a paginated list of all users. Passwords are never included.

**Response 200**
```json
{
  "info": { ... },
  "results": [
    {
      "id": 1,
      "role": "admin",
      "full_name": "Danny LM",
      "email": "danny@anigura.mx",
      "rfc": null,
      "created_at": "2026-02-23 09:05:14",
      "updated_at": "2026-02-23 09:05:14"
    }
  ]
}
```

---

#### GET /users/:id
🔒 Admin — Returns a specific user by ID.

**URL Params**
| Param | Type | Description |
|-------|------|-------------|
| `id` | int | User ID |

**Errors**
| Code | Message |
|------|---------|
| 404 | User not found |

---

#### POST /users/search
🔒 Auth — Finds a user by email address.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `email` | string | ✱ | Email to search |

**Errors**
| Code | Message |
|------|---------|
| 404 | User with email X not found |

---

#### PATCH /users/:id
🔒 Admin — Partially updates a user.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `full_name` | string | ? | max 255 chars |
| `email` | string | ? | Valid email, max 150 chars |
| `rfc` | string | ? | max 13 chars |

**Errors**
| Code | Message |
|------|---------|
| 400 | No valid fields provided for update |
| 404 | User not found |

---

#### DELETE /users/:id
🔒 Admin — Deletes a user permanently.

**Errors**
| Code | Message |
|------|---------|
| 404 | User not found |

---

### Franchises

A franchise is the parent brand (e.g., "Frieren", "Bocchi the Rock!").

#### GET /franchises
🌐 Public — Returns all franchises paginated.

#### GET /franchises/:id
🌐 Public — Returns a single franchise.

**Errors**
| Code | Message |
|------|---------|
| 404 | Franchise not found |

---

#### POST /franchises
🔒 Admin — Creates a new franchise.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | ✱ | max 255 chars |
| `synopsis` | string | ? | Brief description |

**Response 201**
```json
{ "id": 10 }
```

---

#### PATCH /franchises/:id
🔒 Admin — Partially updates a franchise.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | ? | max 255 chars |
| `synopsis` | string | ? | |

**Errors**
| Code | Message |
|------|---------|
| 404 | Franchise not found |

---

#### DELETE /franchises/:id
🔒 Admin — Deletes a franchise.

**Errors**
| Code | Message |
|------|---------|
| 404 | Franchise not found |

---

### Publishers

#### GET /publishers
🌐 Public — Returns all publishers paginated.

#### GET /publishers/:id
🌐 Public — Returns a single publisher.

#### POST /publishers
🔒 Admin — Creates a publisher.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | ✱ | Unique, max 255 chars |

**Response 201**
```json
{ "id": 5 }
```

**Errors**
| Code | Message |
|------|---------|
| 409 | Publisher already exists |

---

#### PATCH /publishers/:id
🔒 Admin — Updates a publisher name.

**Body**
| Field | Type | Required |
|-------|------|----------|
| `name` | string | ? |

---

#### DELETE /publishers/:id
🔒 Admin — Deletes a publisher.

---

### Media Entries

A media entry is a specific work within a franchise (e.g., the manga of Frieren, the anime of Bocchi).

#### GET /media_entries
🌐 Public — Returns all media entries paginated.

#### GET /media_entries/:id
🌐 Public — Returns a single media entry.

#### POST /media_entries
🔒 Admin — Creates a media entry.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `id_franchise` | int | ✱ | Must exist |
| `media_type` | string | ✱ | `manga`, `light_novel`, `anime`, `game` |
| `title` | string | ✱ | max 255 chars |
| `author` | string | ? | max 255 chars |
| `volumes` | int | ? | Number of volumes |
| `episodes` | int | ? | Number of episodes |

**Response 201**
```json
{ "id": 14 }
```

**Errors**
| Code | Message |
|------|---------|
| 400 | Invalid media_type. Allowed: manga, light_novel, anime, game |
| 404 | Franchise not found |

---

#### PATCH /media_entries/:id
🔒 Admin — Partially updates a media entry. All fields optional.

**Errors**
| Code | Message |
|------|---------|
| 404 | MediaEntry not found |

---

#### DELETE /media_entries/:id
🔒 Admin — Deletes a media entry.

---

### Products

Products have 3 types, each with different detail fields.

#### GET /products
🌐 Public — Returns all products paginated. Each product includes its `details` object and `cover_image`.

**Response 200 example**
```json
{
  "info": { ... },
  "results": [
    {
      "id": 1,
      "id_franchise": 1,
      "product_type": "manga_volume",
      "name": "Frieren Vol. 1",
      "description": "...",
      "price": "12.99",
      "discount": "0.00",
      "stock": 25,
      "active": 1,
      "sku": "MNG-FRR-001",
      "details": {
        "id_product": 1,
        "id_publisher": 3,
        "id_media": 1,
        "volume": 1
      },
      "cover_image": "https://..."
    }
  ]
}
```

---

#### GET /products/:id
🌐 Public — Returns a single product with its details and cover image.

---

#### POST /products
🔒 Admin — Creates a product. The `details` object changes based on `product_type`.

**Common body fields**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `id_franchise` | int | ✱ | Must exist |
| `product_type` | string | ✱ | `manga_volume`, `figure`, `setbox` |
| `name` | string | ✱ | max 255 chars |
| `price` | float | ✱ | Must be >= 0 |
| `stock` | int | ✱ | Must be >= 0 |
| `details` | object | ✱ | See below |
| `description` | string | ? | |
| `discount` | float | ? | Percentage, default 0 |
| `active` | bool | ? | Default true |
| `sku` | string | ? | Unique, max 60 chars |

**details — manga_volume**
| Field | Type | Required |
|-------|------|----------|
| `id_publisher` | int | ✱ |
| `id_media` | int | ? |
| `volume` | int | ? |

**details — figure**
| Field | Type | Required |
|-------|------|----------|
| `brand` | string | ✱ max 100 |
| `scale` | string | ? max 20 |

**details — setbox**
| Field | Type | Required |
|-------|------|----------|
| `content` | string | ✱ |
| `id_media` | int | ? |
| `is_limited` | bool | ? Default false |

**Response 201**
```json
{ "id": 12 }
```

**Errors**
| Code | Message |
|------|---------|
| 400 | Invalid product type |
| 400 | Price cannot be negative |
| 400 | Stock cannot be negative |

---

#### PATCH /products/:id
🔒 Admin — Partially updates a product. You can update both base fields and `details` in the same request.

**Errors**
| Code | Message |
|------|---------|
| 404 | Product not found |

---

#### DELETE /products/:id
🔒 Admin — Deletes a product and its details (CASCADE).

---

### Product Images

#### GET /images
🌐 Public — Returns all images paginated.

#### GET /images/:id
🌐 Public — Returns a single image.

#### GET /images/cover/:id
🌐 Public — Returns the cover image of a product. Returns `null` if none is set.

**URL Params**
| Param | Description |
|-------|-------------|
| `id` | Product ID |

#### GET /images/product/:id
🌐 Public — Returns all images for a product.

---

#### POST /images
🔒 Admin — Adds an image to a product. If `is_cover: true`, any previous cover is automatically unset.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `id_product` | int | ✱ | Must exist |
| `image_url` | string | ✱ | max 500 chars |
| `is_cover` | bool | ? | Default false |

**Response 201**
```json
{ "id": 11 }
```

**Errors**
| Code | Message |
|------|---------|
| 404 | Product not found |

---

#### PATCH /images/:id
🔒 Admin — Updates an image. Useful for setting/unsetting cover.

**Body**
| Field | Type | Required |
|-------|------|----------|
| `id_product` | int | ? |
| `image_url` | string | ? max 500 |
| `is_cover` | bool | ? |

---

#### DELETE /images/:id
🔒 Admin — Removes an image.

---

### Addresses

#### GET /addresses
🔒 Admin — Returns all addresses paginated.

#### GET /addresses/me
🔒 Auth — Returns the default address(es) of the authenticated user.

#### GET /addresses/:id
🔒 Auth — Returns a specific address by ID.

---

#### POST /addresses
🔒 Auth — Creates a new address for the authenticated user. If `is_default: true`, any previous default is automatically unset.

> The `id_user` is taken from the token automatically — no need to send it in the body.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `alias` | string | ? | Nickname like "Home", max 50 |
| `street` | string | ✱ | max 255 |
| `city` | string | ✱ | max 100 |
| `state` | string | ✱ | max 100 |
| `zip_code` | string | ✱ | max 10 |
| `is_default` | bool | ? | Default false |

**Response 201**
```json
{ "id": 6 }
```

---

#### PATCH /addresses/:id
🔒 Auth — Partially updates an address.

---

#### DELETE /addresses/:id
🔒 Auth — Deletes an address.

---

### Cart

The cart belongs to the authenticated user — no need to send a user ID anywhere. The token identifies who you are.

#### GET /cart
🔒 Auth — Returns the current user's cart with prices, discounts and subtotals calculated.

**Response 200**
```json
[
  {
    "cart_item_id": 13,
    "id_product": 1,
    "quantity": 2,
    "name": "Frieren Vol. 1",
    "price": "12.99",
    "discount": "0.00",
    "unit_price": "12.99",
    "subtotal": "25.98",
    "cover_image": "https://..."
  }
]
```

---

#### GET /cart/validate
🔒 Auth — Checks each cart item against current stock and availability. Useful before checkout.

Each item gets a `status` field:

| Status | Meaning |
|--------|---------|
| `ok` | Product available and enough stock |
| `unavailable` | Product is inactive or deleted |
| `insufficient` | Stock is lower than cart quantity |

Also returns `available` with the current stock count.

---

#### POST /cart
🔒 Auth — Adds a product to the cart. If the product is already in the cart, the quantities are combined.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `id_product` | int | ✱ | Must exist and be active |
| `quantity` | int | ? | Default 1 |

**Errors**
| Code | Message |
|------|---------|
| 400 | Only {n} units available |
| 400 | Max stock reached in cart |
| 404 | Product unavailable |

---

#### PATCH /cart/:id_item
🔒 Auth — Updates the quantity of a cart item. Setting `quantity: 0` removes the item.

**Body**
| Field | Type | Required |
|-------|------|----------|
| `quantity` | int | ✱ |

**Errors**
| Code | Message |
|------|---------|
| 400 | Only {n} units available |
| 404 | Item not found |

---

#### DELETE /cart/:id_item
🔒 Auth — Removes an item from the cart.

**Errors**
| Code | Message |
|------|---------|
| 404 | Item not found |

---

### Orders

#### GET /orders
🔒 Admin — Returns all orders from all users, paginated.

---

#### POST /orders
🔒 Auth — Creates an order from the current cart. This is an atomic operation:
1. Locks and validates stock for every item
2. Creates the order with a shipping address snapshot
3. Creates order details with price snapshots
4. Discounts stock for each product
5. Clears the cart

If anything fails, the entire operation is rolled back.

> Supports `X-Idempotency-Key` header to prevent duplicate orders.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `id_address` | int | ✱ | Must belong to the authenticated user |

**Response 201**
```json
{ "id": 4 }
```

**Errors**
| Code | Message |
|------|---------|
| 400 | Cart is empty |
| 400 | Insufficient stock for '{product}' |
| 400 | Product '{product}' is no longer available |
| 403 | Access denied (address belongs to someone else) |
| 404 | Address not found |
| 409 | Request is being processed, please wait |

---

#### GET /orders/me
🔒 Auth — Returns the authenticated user's orders, most recent first, paginated.

**Response 200**
```json
{
  "info": { ... },
  "results": [
    {
      "id": 4,
      "id_user": 2,
      "shipping_addr": "Home, 123 Sakura St, Tokyo, Tokyo, 100-0001",
      "total_amount": "124.46",
      "status": "pending",
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```

---

#### GET /orders/me/:id_order
🔒 Auth — Returns a specific order with its full detail (products, quantities, unit prices).

**Response 200**
```json
{
  "id": 4,
  "shipping_addr": "...",
  "total_amount": "124.46",
  "status": "pending",
  "details": [
    {
      "id": 1,
      "id_product": 1,
      "quantity": 2,
      "unit_price": "12.99",
      "name": "Frieren Vol. 1",
      "sku": "MNG-FRR-001"
    }
  ]
}
```

**Errors**
| Code | Message |
|------|---------|
| 403 | Access denied (order belongs to someone else) |
| 404 | Order not found |

---

#### PATCH /orders/me/:id_order/cancel
🔒 Auth — Cancels a pending order and restores stock for all products.

> Only orders with `status: pending` can be cancelled.
> Supports `X-Idempotency-Key` header to prevent double stock refunds.

**Errors**
| Code | Message |
|------|---------|
| 400 | Only pending orders can be cancelled |
| 403 | Access denied |
| 404 | Order not found |
| 409 | Request is being processed, please wait |

---

#### PATCH /orders/:id_order
🔒 Admin — Updates an order's status or shipping address.

**Body**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `status` | string | ? | `pending`, `paid`, `shipped`, `cancelled` |
| `shipping_addr` | string | ? | max 500 |

**Errors**
| Code | Message |
|------|---------|
| 400 | Cannot update a cancelled order |
| 404 | Order not found |

---

## Error Reference

| Code | Meaning |
|------|---------|
| 400 | Bad Request — missing or invalid fields |
| 401 | Unauthorized — missing, invalid or expired token |
| 403 | Forbidden — valid token but insufficient permissions |
| 404 | Not Found — resource doesn't exist |
| 409 | Conflict — duplicate resource or idempotency lock |
| 500 | Internal Server Error — something went wrong on the server |

---

*Anigura API · Built with PHP 8.2 · MySQL 8.0 · No frameworks — pure PHP 🐘*
