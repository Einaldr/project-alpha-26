import api from "@/lib/api";
import type { GitAuthType, Project, ProjectPermissions } from "@/types/api";

export const projectService = {
    fetchProjects: async (groupId: string, params?: object): Promise<Project[]> => {
        const {data} = await api.get(`/groups/${groupId}/projects`, {params});
        return data.data;
    },

    fetchProject: async (groupId: string, projectId: string): Promise<Project> => {
        const {data} = await api.get(`/groups/${groupId}/projects/${projectId}`)
        return data.data
    },

    fetchProjectPermissions: async (groupId: string, projectId: string): Promise<ProjectPermissions[]> => {
        const {data} = await api.get(`/groups/${groupId}/projects/${projectId}/permissions`);
        return data.permissions
    },

    fetchAvailableBranches: async (groupId: string, projectId: string): Promise<{current: string, branches: string[]}> => {
        const {data} = await api.get(`/groups/${groupId}/projects/${projectId}/branches`)
        return data
    },

    updateProject: async (groupId: string, projectId: string, formData: FormData): Promise<Project> => {
        const {data} = await api.post(`/groups/${groupId}/projects/${projectId}`, formData, { headers: {'Content-Type': 'multipart/form-data'}})
        return data
    },

    deleteProject: async (groupId: string, projectId: string): Promise<void> => {
        const {data} = await api.delete(`/groups/${groupId}/projects/${projectId}`)
        return data.message
    },

    fetchSecrets: async (groupId: string, projectId: string): Promise<{auth_type: GitAuthType, is_configured: boolean}> => {
        const {data} = await api.get(`/groups/${groupId}/projects/${projectId}/secrets`)
        return data.data
    }
}