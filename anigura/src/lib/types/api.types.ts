
export interface ApiResponse<T> {
    status:    "success"|"error";
    code:      number;
    msg:       string;
    data:      T|null;
    timestamp: string;
}

export interface PaginationInfo {
    total:   number;
    pages:   number;
    current: number;
    next:    number|null;
    prev:    number|null;
}

export interface Paginated<T> {
    info:    PaginationInfo;
    results: T[];
}

export interface PaginationParams {
    page?:  number;
    limit?: number;
}

