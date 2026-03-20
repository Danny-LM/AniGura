import type { ToastType, ToastMsg } from "../types";

class UiStore {
    globalLoading = $state(false);
    toasts        = $state<ToastMsg[]>([]);

    /**
     * To activate and load the global loader in full screen
     * @param isLoading 
     */
    setLoading(isLoading: boolean) {
        this.globalLoading = isLoading;
    }

    /**
     * Show a toas and deletes before the time specified
     * @param msg 
     * @param type 
     * @param durationMs 
     */
    showToast(msg: string, type: ToastType = "info", durationMs: number = 3000) {
        const id = crypto.randomUUID();
        this.toasts.push({ id, msg, type });

        setTimeout(() => this.removeToast(id), durationMs);
    }

    removeToast(id: string) {
        this.toasts = this.toasts.filter(t => t.id !== id);
    }
}

export const uiStore = new UiStore();

