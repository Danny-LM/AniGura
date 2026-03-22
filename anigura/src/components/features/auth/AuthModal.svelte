<script lang="ts">
    import Modal from "../../ui/Modal.svelte";
    import Button from "../../ui/Button.svelte";
    import { authService } from "../../../lib/services/auth.service";
    import type { LoginRequest, RegisterRequest } from "../../../lib/types";
    import { uiStore } from "../../../lib/stores/ui.store.svelte";
    import { getErrorMsg } from "../../../lib/utils";

    interface Props {
        open:       boolean;
        onClose:    () => void;
        onSuccess?: () => void;
    }

    let { open, onClose, onSuccess }: Props = $props();

    type Tab = "login"|"register";

    let activeTab = $state<Tab>("login");
    let loading   = $state(false);
    let error     = $state<string |null>(null);

    let fullName = $state("");
    let email    = $state("");
    let password = $state("");

    function switchTab(tab: Tab) {
        activeTab = tab;
        error     = null;
        fullName  = "";
        email     = "";
        password  = "";
    }

    function handleClose() {
        switchTab("login");
        onClose();
    }

    async function handleSubmit() {
        error   = null;
        loading = true;

        try {
            if (activeTab === "login") {
                const credential: LoginRequest = { email, password };
                await authService.login(credential);

            } else {
                const data: RegisterRequest = { full_name: fullName, email, password };
                await authService.register(data);
            }

            uiStore.showToast(
                activeTab === "login" ? "Welcome back!" : "Account created!",
                "success"
            );

            handleClose();
            onSuccess?.();

        } catch (err) {
            error = getErrorMsg(err, "Something went wrong");
        } finally {
            loading = false;
        }
    }
</script>

<Modal {open} title={activeTab === "login" ? "Welcome back" : "Create account"} onClose={handleClose}>
    {#snippet children ()}
        <div class="tabs">
            <button
                class="tab-btn"
                class:active={activeTab === "login"}
                onclick={() => switchTab("login")}
            >Login</button>
            <button
                class="tab-btn"
                class:active={activeTab === "register"}
                onclick={() => switchTab("register")}
            >Register</button>
        </div>

        {#if error}
            <p class="form-error">{error}</p>            
        {/if}

        {#if activeTab === "register"}
            <div class="field">
                <label for="fullname">Full name</label>
                <input
                    type="text" id="fullname"
                    bind:value={fullName}
                    placeholder="Eg. John Doe"
                    disabled={loading}
                />
            </div>
        {/if}

        <div class="field">
            <label for="email">Email</label>
            <input
                type="email" id="email"
                bind:value={email}
                placeholder="you@example.com"
                disabled={loading}
            />
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input
                type="password" id="password"
                bind:value={password}
                placeholder="••••••••"
                disabled={loading}
            />
        </div>
    {/snippet}

    {#snippet footer()}
        <div class="footer-action">
            <Button
                variant="primary" size="md" {loading}
                disabled={loading}
                onClick={handleSubmit}
            >
                {activeTab === "login" ? "Login" : "Create account"}
            </Button>
        </div>
    {/snippet}

</Modal>

<style>
    .tabs {
        display: flex;
        width: 100%;
        border-bottom: 1px solid var(--border);
        margin-bottom: var(--space-4);
    }

    .tab-btn {
        flex: 1;
        padding: var(--space-3);
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        color: var(--text-muted);
        font-size: 14px; font-weight: 600;
        transition: all 0.15s;
        text-align: center;
    }

    .tab-btn:hover { color: var(--text-primary); }

    .tab-btn.active {
        color: var(--accent-light);
        border-bottom-color: var(--accent);
    }

    .form-error {
        font-size: 13px;
        color: var(--danger);
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        border-radius: var(--radius-sm);
        padding: var(--space-2) var(--space-3);
        margin-bottom: var(--space-2);
    }

    .field {
        display: flex; flex-direction: column;
        gap: var(--space-1);
        margin-bottom: var(--space-3);
    }

    .footer-action { width: 100%; }
    .footer-action :global(button) { width: 100%; }

    label {
        font-size: 12px; font-weight: 600;
        color: var(--text-secondary);
        letter-spacing: 0.3px;
    }

    input {
        background: var(--bg-elevated);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        font-size: 14px;
        padding: var(--space-2) var(--space-3);
        transition: border-color 0.15s;
        outline: none;
    }

    input:focus { border-color: var(--accent); }
    input:disabled { opacity: 0.5; }
</style>

