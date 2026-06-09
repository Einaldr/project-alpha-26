import api from "@/lib/api";
import type { Project } from "@/types/api";

export const projectService = {
    fetchProjects: async (groupId: string, params?: object): Promise<Project[]> => {
        const {data} = await api.get(`/groups/${groupId}/projects`, {params});
        return data.data;
    },

    fetchProject: async (groupId: string, projectId: string): Promise<Project> => {
        const {data} = await api.get(`/groups/${groupId}/projects/${projectId}`)
        return data.data
    }
}