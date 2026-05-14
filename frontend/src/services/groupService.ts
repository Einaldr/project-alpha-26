import api from "@/lib/api";
import type { Group } from "@/types/api";

export const  groupService = {
    index: async (params?: object) => {
        const {data} = await api.get<{data: Group[]}>("/groups", {params});
        return data.data;
    },

    myGroups: async () => {
        const {data} = await api.get<{data: Group}>('/me/groups');
        return data.data;
    },

    show: async (id: string) => {
        const {data} = await api.get<{data: Group}>(`/groups/${id}`);
        return data.data;
    },

    store: async (formData: FormData) => {
        const {data} = await api.post<{data: Group}>('/groups', formData);
        return data.data;
    },

    update: async (id: string, formData: FormData) => {
        formData.append('_method', 'PATCH');
        const {data} = await api.post(`/groups/${id}`, formData);
        return data.data;
    }
}