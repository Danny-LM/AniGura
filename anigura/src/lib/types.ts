
export interface Response<T> {
    status: string,
    code: number,
    msg: string,
    data: T|null,
    timestamp: Date
}

export interface User {
    id: number,
    role: "user" | "admin",
    full_name: string,
    email: string
}

export interface Product {
    id: number,
    id_franchise: number,
    product_type: "manga_volume"|"setbox"|"figure",
    name: string,
    cover_image: string|null,
    description: string|null,
    price: string
    discount: string
    stock: number,
    active: boolean,
    sku: string|null
    details: MangaDetails|BoxsetDetails|FigureDetails,
    created_at: Date,
    updated_at: Date
}

export interface CartItem {
    cart_item_id: number,
    id_product: number,
    name: string,
    cover_image: string|null,
    quantity: number,
    stock: number,
    active: boolean,
    price: string,
    discount: string,
    unit_price: string,
    subtotal: string,
}

interface MangaDetails {
    id_product: number,
    id_publisher: number,
    id_media: number,
    volume: number
}

interface BoxsetDetails {
    id_product: number,
    id_media: number,
    content: string,
    is_limited: boolean
}

interface FigureDetails {
    id_product: number,
    brand: string|null,
    scale: number|null,
}
