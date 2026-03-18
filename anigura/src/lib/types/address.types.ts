
export interface Address {
    id:         number;
    id_user:    number;
    alias:      string|null;
    street:     string;
    city:       string;
    state:      string;
    zip_code:   string;
    is_default: 0 | 1;
    created_at: string;
    updated_at: string;
}

export interface CreateAddressRequest {
    alias?:      string;
    street:      string;
    city:        string;
    state:       string;
    zip_code:    string;
    is_default?: boolean;
}

export type UpdateAddressRequest = Partial<CreateAddressRequest>;

