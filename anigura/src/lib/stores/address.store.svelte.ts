import type { Address } from "../types";

class AddressStore {
    addresses = $state<Address[]>([]);

    /**
     * To replace the list of addresses
     * @param newAddresses 
     */
    setAddresses(newAddresses: Address[]) {
        this.addresses = newAddresses;
    }

    /**
     * To add a new address to the store
     * @param newAddress 
     */
    addAddress(newAddress: Address) {
        if (newAddress.is_default === 1) this.clearDefaults();
        this.addresses.push(newAddress);
    }

    /**
     * To update an existing address
     * @param id 
     * @param updatedData 
     */
    updateAddress(id:number, updatedData:Partial<Address>) {
        if (updatedData.is_default === 1) this.clearDefaults();

        const index = this.addresses.findIndex(addr => addr.id === id);
        if (index !== -1) {
            this.addresses[index] = { ...this.addresses[index], ...updatedData };
        }
    }

    /**
     * To remove an address
     * @param id 
     */
    removeAddress(id:number) {
        this.addresses = this.addresses.filter(addr => addr.id !== id);
    }

    private clearDefaults() {
        this.addresses = this.addresses.map(addr => ({
            ...addr,
            is_default: 0
        }));
    }
}

export const addressStore = new AddressStore();

