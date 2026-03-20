import type { ToastType } from "../../components/ui/Toast.svelte";

export interface ToastMsg {
    id:   string;
    msg:  string;
    type: ToastType;
}

class UiStore {
    globalLoading = $state(false);
    toasts = $state<ToastMsg[]>([]);

    setLoading(isLoading: boolean) {
        this.globalLoading = isLoading;
    }

    showToast(msg: string, type: ToastType="info", durationMs:number=3000) {
        const id = crypto.randomUUID();
        this.toasts.push({ id, msg, type });

        setTimeout(() => {
            this.removeToast(id);
        }, durationMs);
    }

    removeToast(id: string) {
        this.toasts = this.toasts.filter(toast => toast.id !== id);
    }
}

export const uiStore = new UiStore();
