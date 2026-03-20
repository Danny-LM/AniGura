import { apiClient, ENDPOINTS } from "../api";
import { uiStore } from "../stores/ui.store";
import { addressStore } from "../stores/address.store";
import { authStore } from "../stores/auth.store";
import type { Address, CreateAddressRequest, UpdateAddressRequest } from "../types";

export class AddressService {
    constructor(private api = apiClient) {}

    /**
     * To get all user addresses
     */
    async loadMyAddresses(): Promise<void> {
        uiStore.setLoading(true);
        try {
            const response = await this.api.get<Address[]>(ENDPOINTS.ADDRESSES.ME);
            
            if (response.data) {
                addressStore.setAddresses(response.data);
            }
        } catch (error) {
            uiStore.showToast("Failed to load your addresses", "error");
        } finally {
            uiStore.setLoading(false);
        }
    }

    /**
     * To add a new user address
     * @param request 
     */
    async createAddress(request: CreateAddressRequest): Promise<void> {
        try {
            const response = await this.api.post<{ id: number }>(ENDPOINTS.ADDRESSES.BASE, request);
            
            if (response.data) {
                const newAddress: Address = {
                    id: response.data.id,
                    id_user: authStore.currentUser?.id || 0,
                    ...request,
                    alias: request.alias ?? null,
                    is_default: request.is_default ?? 0,
                    created_at: new Date().toISOString(),
                    updated_at: new Date().toISOString(),
                };

                addressStore.addAddress(newAddress);
                uiStore.showToast("Address saved successfully", "success");
            }
        } catch (error: any) {
            const msg = error.response?.data?.msg || "Failed to save address";
            uiStore.showToast(msg, "error");
        }
    }

    async updateAddress(id:number, request:UpdateAddressRequest): Promise<void> {
        try {
            await this.api.patch(`${ENDPOINTS.ADDRESSES.BASE}/${id}`, request);
            
            addressStore.updateAddress(id, request);
            uiStore.showToast("Address updated", "success");
            
        } catch (error: any) {
            const msg = error.response?.data?.msg || "Failed to update address";
            uiStore.showToast(msg, "error");
            throw error;
        }
    }

    async deleteAddress(id: number): Promise<void> {
        try {
            await this.api.delete(`${ENDPOINTS.ADDRESSES.BASE}/${id}`);
            
            addressStore.removeAddress(id);
            uiStore.showToast("Address deleted", "success");
            
        } catch (error: any) {
            const msg = error.response?.data?.msg || "Failed to delete address";
            uiStore.showToast(msg, "error");
        }
    }
}

export const addressService = new AddressService();

