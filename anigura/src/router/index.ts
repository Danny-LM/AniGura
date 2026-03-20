import { wrap } from "svelte-spa-router/wrap";
import { push } from "svelte-spa-router";
import type { GuardType } from "./guards";
import { useGuard } from "./guards";


function withGuard(guard: GuardType) {
    return {
        conditions: [
            () => useGuard(guard).allow
        ],
        onConditionsFailed: () => {
            const result = useGuard(guard);
            if (result.redirectTo) push(result.redirectTo);
        }
    };
}

export const routes = {
    // PUBLIC
    "/": wrap({
        asyncComponent: () => import("../views/HomeView.svelte"),
    }),

    // AUTH
    "/cart": wrap({
        asyncComponent: () => import("../views/CartView.svelte"),
        ...withGuard("auth")
    }),

    "/orders": wrap({
        asyncComponent: () => import("../views/OrdersView.svelte"),
        ...withGuard("auth")
    }),

    "/orders/:id": wrap({
        asyncComponent: () => import("../views/OrderDetailView.svelte"),
        ...withGuard("auth")
    }),

    "/profile": wrap({
        asyncComponent: () => import("../views/ProfileView.svelte"),
        ...withGuard("auth")
    }),

    // ADMIN
    "/admin/reports": wrap({
        asyncComponent: () => import("../views/HomeView.svelte"), // placeholder
        ...withGuard("admin")
    }),

    // 404
    "*": wrap({
        asyncComponent: () => import("../views/NotFoundView.svelte"),
    }),

};

