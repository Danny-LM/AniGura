interface CacheEntry<T> {
    data: T;
    timestamp: number;
}

class CacheStore {
    private cache = $state(new Map<string, CacheEntry<unknown>>());
    private defaulTTL = 5*60*1000;

    set<T>(key: string, data: T) {
        this.cache.set(key, { data, timestamp: Date.now() });
    }

    get<T>(key: string): T|null {
        const entry = this.cache.get(key);
        if (!entry) return null;

        const isExpired = Date.now() - entry.timestamp > this.defaulTTL;

        if (isExpired) {
            this.cache.delete(key);
            return null;
        }

        return entry.data as T;
    }

    clear() {
        this.cache.clear();
    }
}

export const cacheStore = new CacheStore();
