import api from "@/lib/api"
import type { File, Tree } from "@/types/api"

export const fileService = {
  fetchDir: async (
    groupId: string,
    projectId: string,
    pathname?: string
  ): Promise<Tree> => {
    if (pathname && pathname != "") {
      const { data } = await api.get(
        `/groups/${groupId}/projects/${projectId}/files?path=${pathname}`
      )
      return data
    } else {
      const { data } = await api.get(
        `/groups/${groupId}/projects/${projectId}/files`
      )
      return data
    }
  },

  fetchFile: async (
    groupId: string,
    projectId: string,
    pathname: string,
    filename: string
  ): Promise<File> => {
    const fullPath = pathname ? `${pathname}/${filename}` : filename

    const { data } = await api.get<File>(
      `/groups/${groupId}/projects/${projectId}/files/show?path=${encodeURIComponent(fullPath)}`
    )
    return data
  },
}
