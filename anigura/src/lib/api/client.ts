import axios from "axios";
import type { AxiosInstance, AxiosRequestConfig, InternalAxiosRequestConfig } from "axios";
import type { ApiResponse } from "../types";
import { Storage } from "../utils/storage";
import { isTokenExpired } from "../utils/token";
import { ENDPOINTS } from "../api";


class ApiClient {
  private static instance: ApiClient;
  private readonly http: AxiosInstance;
  private readonly baseUrl: string;
  private isRefreshing = false;
  private refreshQueue: Array<{
    resolve: (token: string) => void;
    reject: (error: unknown) => void;
  }> = [];

  private constructor() {
    this.baseUrl = import.meta.env.VITE_API_URL;
    this.http = axios.create({
      baseURL: this.baseUrl,
      headers: {
        "Content-Type": "application/json",
      },
      timeout: 10000,
    });

    this.setupRequestInterceptor();
    this.setupResponseInterceptor();
  }

  /**
   * To get a single instance of ApiClient
   * @returns 
   */
  static getInstance(): ApiClient {
    if (!ApiClient.instance) {
      ApiClient.instance = new ApiClient();
    }
    return ApiClient.instance;
  }

  /**
   * To set up the request interceptor to automatically add the
   * Authorization header and handle the refresh token
   */
  private setupRequestInterceptor(): void {
    this.http.interceptors.request.use(
      async (config: InternalAxiosRequestConfig) => {
        const accessToken = Storage.getAccessToken();

        if (accessToken) {
          if (isTokenExpired(accessToken)) {
            const newToken = await this.tryRefresh();
            if (newToken) {
              config.headers.Authorization = `Bearer ${newToken}`;
            }
          } else {
            config.headers.Authorization = `Bearer ${accessToken}`;
          }
        }

        return config;
      },
      (error) => Promise.reject(error)
    );
  }

  /**
   * To set up the response interceptor to handle errors
   */
  private setupResponseInterceptor(): void {
    this.http.interceptors.response.use(
      (response) => response,

      async (error) => {
        const originalRequest = error.config as InternalAxiosRequestConfig & {
          _retry?: boolean;
        };

        const is401 = error.response?.status === 401;
        const isRetry = originalRequest._retry;
        const isRefreshEndpoint = originalRequest.url?.includes(ENDPOINTS.AUTH.REFRESH);

        if (is401 && !isRetry && !isRefreshEndpoint) {
          originalRequest._retry = true;

          if (this.isRefreshing) {
            return new Promise((resolve, reject) => {
              this.refreshQueue.push({ resolve, reject });
            }).then((token) => {
              originalRequest.headers.Authorization = `Bearer ${token}`;
              return this.http(originalRequest);
            });
          }

          const newToken = await this.tryRefresh();

          if (newToken) {
            this.processQueue(newToken);
            originalRequest.headers.Authorization = `Bearer ${newToken}`;
            return this.http(originalRequest);
          }

          this.processQueue(null);
          Storage.clearSession();
          window.dispatchEvent(new CustomEvent("auth:sessionExpired"));
          return Promise.reject(error);
        }

        return Promise.reject(error);
      }
    );
  }

  /**
   * To attempt to refresh the access token using the refresh token.
   * @returns 
   */
  private async tryRefresh(): Promise<string | null> {
    const refreshToken = Storage.getRefreshToken();
    if (!refreshToken) return null;

    this.isRefreshing = true;

    try {
      const response = await axios.post<ApiResponse<{ access_token: string }>>(
        `${this.baseUrl}${ENDPOINTS.AUTH.REFRESH}`,
        { refresh_token: refreshToken },
        { headers: { "Content-Type": "application/json" } }
      );

      const newAccessToken = response.data.data?.access_token;
      if (!newAccessToken) return null;

      Storage.setTokens({
        access_token: newAccessToken,
        refresh_token: refreshToken,
      });

      return newAccessToken;

    } catch {
      return null;
    } finally {
      this.isRefreshing = false;
    }
  }

  /**
   * To process the queue of pending requests waiting for a token
   * refresh to complete.
   * @param token 
   */
  private processQueue(token: string | null): void {
    this.refreshQueue.forEach(({ resolve, reject }) => {
      if (token) {
        resolve(token);
      } else {
        reject(new Error("Session expired"));
      }
    });

    this.refreshQueue = [];
  }

  /**
   * To send a GET request to a URL with optional config.
   * @param url 
   * @param config 
   * @returns 
   */
  async get<T>(url: string, config?: AxiosRequestConfig): Promise<ApiResponse<T>> {
    const response = await this.http.get<ApiResponse<T>>(url, config);
    return response.data;
  }

  /**
   * To send a POST request to a URL with optional data and config.
   * @param url 
   * @param data 
   * @param config 
   * @returns 
   */
  async post<T>(url: string, data?: unknown, config?: AxiosRequestConfig): Promise<ApiResponse<T>> {
    const response = await this.http.post<ApiResponse<T>>(url, data, config);
    return response.data;
  }

  /**
   * To send a PATCH request to a URL with optional data and config.
   * @param url 
   * @param data 
   * @param config 
   * @returns 
   */
  async patch<T>(url: string, data?: unknown, config?: AxiosRequestConfig): Promise<ApiResponse<T>> {
    const response = await this.http.patch<ApiResponse<T>>(url, data, config);
    return response.data;
  }

  /**
   * To send a DELETE request to a URL with optional config.
   * @param url 
   * @param config 
   * @returns 
   */
  async delete<T>(url: string, config?: AxiosRequestConfig): Promise<ApiResponse<T>> {
    const response = await this.http.delete<ApiResponse<T>>(url, config);
    return response.data;
  }
}

export const apiClient = ApiClient.getInstance();

