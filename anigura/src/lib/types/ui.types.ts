export type ToastType = "success"|"error"|"info"|"warning";

export interface ToastMsg {
    id:   string;
    type: ToastType;
    msg:  string;
}

