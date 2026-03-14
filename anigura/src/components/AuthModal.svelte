<script lang="ts">
    import { API } from "../lib/api";
    import { authStore } from "../lib/stores/authStore";

    interface Props {
        open: boolean;
        onClose: () => void;
        onSuccess: () => void;
    }

    let { open, onClose, onSuccess }: Props = $props();

    type Tab = "login" | "register";
    let activeTab = $state<Tab>("login");

    // Fields
    let fullName = $state("");
    let email    = $state("");
    let password = $state("");

    // UI state
    let loading = $state(false);
    let error   = $state<string | null>(null);

    function switchTab(tab: Tab) {
        activeTab = tab;
        error = null;
        fullName = "";
        email = "";
        password = "";
    }

    function handleClose() {
        switchTab("login");
        onClose();
    }

    async function handleSubmit() {
        error = null;
        loading = true;

        try {
            if (activeTab === "login") {
                const user = await API.login(email, password);
                authStore.login(user);
            } else {
                const user = await API.register(fullName, email, password);
                authStore.login(user);
            }
            onSuccess();
        } catch (e) {
            error = e instanceof Error ? e.message : "Something went wrong";
        } finally {
            loading = false;
        }
    }

    // Close on backdrop click
    function handleBackdrop(e: MouseEvent) {
        if ((e.target as HTMLElement).classList.contains("backdrop")) {
            handleClose();
        }
    }
</script>

{#if open}
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div class="backdrop" onclick={handleBackdrop}>
        <div class="modal">

            <!-- Header -->
            <div class="modal-header">
                <h2 class="modal-title">
                    {activeTab === "login" ? "Welcome back" : "Create account"}
                </h2>
                <button class="btn-close" onclick={handleClose} aria-label="Close">✕</button>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <button
                    class="tab-btn"
                    class:active={activeTab === "login"}
                    onclick={() => switchTab("login")}
                >
                    Login
                </button>
                <button
                    class="tab-btn"
                    class:active={activeTab === "register"}
                    onclick={() => switchTab("register")}
                >
                    Register
                </button>
            </div>

            <!-- Form -->
            <div class="modal-body">
                {#if error}
                    <p class="form-error">{error}</p>
                {/if}

                {#if activeTab === "register"}
                    <div class="field">
                        <label for="fullname">Full name</label>
                        <input
                            id="fullname"
                            type="text"
                            bind:value={fullName}
                            placeholder="Your name"
                            disabled={loading}
                        />
                    </div>
                {/if}

                <div class="field">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        bind:value={email}
                        placeholder="you@example.com"
                        disabled={loading}
                    />
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        type="password"
                        bind:value={password}
                        placeholder="••••••••"
                        disabled={loading}
                    />
                </div>

                <button
                    class="btn-submit"
                    onclick={handleSubmit}
                    disabled={loading}
                >
                    {loading ? "Loading..." : activeTab === "login" ? "Login" : "Create account"}
                </button>
            </div>

        </div>
    </div>
{/if}

<style>
    .backdrop {
        position: fixed; inset: 0; z-index: 200;
        background: rgba(0, 0, 0, 0.7);
        display: flex; align-items: center; justify-content: center;
        backdrop-filter: blur(4px);
    }

    .modal {
        background: var(--bg-surface);
        border: 1px solid var(--border); border-radius: var(--radius-lg);
        width: 100%; max-width: 380px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .modal-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: var(--space-4) var(--space-5);
        border-bottom: 1px solid var(--border);
    }

    .modal-title {
        font-size: 16px; font-weight: 700;
        color: var(--text-primary);
    }

    .btn-close {
        background: transparent;
        border: none; border-radius: var(--radius-sm);
        color: var(--text-muted);
        font-size: 14px;
        padding: var(--space-1);
        transition: color 0.15s;
    }

    .btn-close:hover {
        color: var(--text-primary);
    }

    .tabs {
        display: flex;
        border-bottom: 1px solid var(--border);
    }

    .tab-btn {
        flex: 1;
        padding: var(--space-3);
        background: transparent;
        border: none; border-bottom: 2px solid transparent;
        color: var(--text-muted);
        font-size: 14px; font-weight: 600;
        transition: all 0.15s;
    }

    .tab-btn:hover {
        color: var(--text-primary);
    }

    .tab-btn.active {
        color: var(--accent-light);
        border-bottom-color: var(--accent);
    }

    .modal-body {
        display: flex; flex-direction: column;
        gap: var(--space-3); padding: var(--space-5);
    }

    .form-error {
        font-size: 13px;
        color: var(--danger);
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-sm);
        padding: var(--space-2) var(--space-3);
    }

    .field {
        display: flex; flex-direction: column;
        gap: var(--space-1);
    }

    label {
        font-size: 12px; font-weight: 600;
        color: var(--text-secondary);
        letter-spacing: 0.3px;
    }

    input {
        background: var(--bg-elevated);
        border: 1px solid var(--border); border-radius: var(--radius-sm);
        color: var(--text-primary);
        font-size: 14px;
        padding: var(--space-2) var(--space-3);
        transition: border-color 0.15s;
        outline: none;
    }

    input:focus {
        border-color: var(--accent);
    }

    input:disabled {
        opacity: 0.5;
    }

    .btn-submit {
        width: 100%;
        padding: var(--space-3);
        background: var(--accent);
        color: white;
        border: none; border-radius: var(--radius-sm);
        font-size: 14px; font-weight: 600;
        margin-top: var(--space-1);
        transition: background 0.15s;
    }

    .btn-submit:hover:not(:disabled) {
        background: var(--accent-hover);
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
