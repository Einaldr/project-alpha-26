import api from "@/lib/api";
import type { Group } from "@/types/api";
import type z from "zod";

export const  groupService = {
    index: async (params?: object): Promise<Group[]> => {
        const {data} = await api.get<{data: Group[]}>("/groups", {params});
        return data.data;
    },

    myGroups: async (): Promise<Group[]> => {
        const {data} = await api.get<{data: Group[]}>('/me/groups');
        return data.data;
    },

    workspace: async (): Promise<Group> => {
        const {data} = await api.get<{data: Group}>('/me/workspace');
        return data.data;
    },

    show: async (groupId: string): Promise<Group> => {
        const {data} = await api.get<{data: Group}>(`/groups/${groupId}`);
        return data.data;
    },

    store: async (formData: {name: string, icon: z.core.File, billing_email: string}): Promise<Group> => {
        const {data} = await api.post('/groups', formData);
        return data.data;
    },

    update: async (groupId: string, formData: {name: string, icon: z.core.File, billing_email: string}): Promise<Group> => {
        const {data} = await api.patch(`/groups/${groupId}`, formData);
        return data.data;
    }
}