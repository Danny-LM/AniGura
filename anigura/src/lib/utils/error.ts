export function getErrorMsg(error: unknown, fallback: string): string {
    if (
        error && typeof error === "object" &&
        "response" in error && error.response && typeof error.response === "object" &&
        "data" in error.response && error.response.data && typeof error.response.data === "object" &&
        "msg" in error.response.data && typeof error.response.data.msg === "string"
    ) {
        return error.response.data.msg;
    }
    return fallback;
}

