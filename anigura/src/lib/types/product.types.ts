
export type ProductType = "manga_volume"|"figure"|"setbox";

export interface MangaVolumeDetails {
    id_product:   number;
    id_publisher: number;
    id_media:     number|null;
    volume:       number|null;
}

export interface FigureDetails {
    id_product: number;
    brand:      string;
    scale:      string|null;
}

export interface SetboxDetails {
    id_product: number;
    id_media:   number|null;
    content:    string;
    is_limited: 0 | 1; // aqui me genera duda, no lo combierte en auth a boolean? con recibir el 0 false y 1 true?
}

export type ProductDetails = MangaVolumeDetails|FigureDetails|SetboxDetails;

export interface Product {
    id:           number;
    id_franchise: number;
    product_type: ProductType;
    name:         string;
    description:  string|null;
    price:        string;
    discount:     string;
    stock:        number;
    active:       0 | 1;
    sku:          string|null;
    cover_image:  string|null;
    details:      ProductDetails|null;
    created_at:   string;
    updated_at:   string;
}

export interface CreateProductResponse {
    id: number;
}

