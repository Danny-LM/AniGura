import { apiClient, ENDPOINTS } from "../api";
import { uiStore } from "../stores/ui.store";
import { addressStore } from "../stores/address.store";
import { authStore } from "../stores/auth.store";
import type { Address, CreateAddressRequest, UpdateAddressRequest } from "../types";
import { getErrorMsg } from "../utils";

export class AddressService {
    constructor(private api = apiClient) {}

    /**
     * To get all user addresses
     */
    async loadMyAddresses(): Promise<void> {
        uiStore.setLoading(true);
        try {
            const response = await this.api.get<Address[]>(ENDPOINTS.ADDRESSES.ME);
            if (response.data) addressStore.setAddresses(response.data);

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
            const response = await this.api.post<{ id: number }>(
                ENDPOINTS.ADDRESSES.BASE,
                request
            );
            
            if (response.data) {
                const newAddress: Address = {
                    id:         response.data.id,
                    id_user:    authStore.currentUser?.id ?? 0,
                    alias:      request.alias ?? null,
                    street:     request.street,
                    city:       request.city,
                    state:      request.state,
                    zip_code:   request.zip_code,
                    is_default: request.is_default ?? 0,
                    created_at: new Date().toISOString(),
                    updated_at: new Date().toISOString(),
                };

                addressStore.addAddress(newAddress);
                uiStore.showToast("Address saved successfully", "success");
            }

        } catch (error) {
            uiStore.showToast(getErrorMsg(error, "Failed to save address"), "error");
        }
    }

    /**
     * Partial update an address
     * @param id 
     * @param request 
     */
    async updateAddress(id: number, request: UpdateAddressRequest): Promise<void> {
        try {
            await this.api.patch(ENDPOINTS.ADDRESSES.BY_ID(id), request);
            
            addressStore.updateAddress(id, request);
            uiStore.showToast("Address updated", "success");
            
        } catch (error) {
            uiStore.showToast(getErrorMsg(error, "Failed to update address"), "error");
            throw error;
        }
    }

    /**
     * To delete an address
     * @param id 
     */
    async deleteAddress(id: number): Promise<void> {
        try {
            await this.api.delete(ENDPOINTS.ADDRESSES.BY_ID(id));
            
            addressStore.removeAddress(id);
            uiStore.showToast("Address deleted", "success");
            
        } catch (error) {
            uiStore.showToast(getErrorMsg(error, "Failed to delete address"), "error");
        }
    }
}

export const addressService = new AddressService();

