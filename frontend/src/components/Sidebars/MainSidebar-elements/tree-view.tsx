import { Button } from "@/components/ui/button"
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from "@/components/ui/collapsible"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { useProjectsStore } from "@/hooks/useProjectsStore"
import { fileService } from "@/services/fileService"
import type { TreeFile } from "@/types/api"
import {
  CaretDownIcon,
  CaretRightIcon,
  FileIcon,
  FolderIcon,
  SpinnerIcon,
} from "@phosphor-icons/react"
import { useCallback, useEffect, useRef, useState } from "react"
import { Link, useNavigate, useSearchParams } from "react-router-dom"

export default function TreeView() {
  const navigate = useNavigate()
  const { activeProject } = useProjectsStore()
  const { activeGroup } = useActiveGroupStore()
  const [searchParams] = useSearchParams()

  const projectId = activeProject?.id || null
  const groupId = activeGroup?.id || null

  const activePath = searchParams.get("path") || ""

  const [tree, setTree] = useState<Record<string, TreeFile[]>>({})
  const [loadingPaths, setLoadingPaths] = useState<Record<string, boolean>>({})

  const isMounted = useRef(true)
  useEffect(() => {
    isMounted.current = true
    return () => {
      isMounted.current = false
    }
  }, [])

  const treeRef = useRef<Record<string, TreeFile[]>>({})

  useEffect(() => {
    treeRef.current = tree
  }, [tree])

  useEffect(() => {
    treeRef.current = {}
  }, [projectId])

  const [prevProjectId, setPrevProjectId] = useState<string | null>(null)
  if (projectId !== prevProjectId) {
    setPrevProjectId(projectId ?? null)
    setTree({})
    setLoadingPaths({})
  }

  const fetchDir = useCallback(
    async (path: string) => {
      if (!groupId) return
      if (!projectId) return
      if (tree[path]) return // Cache hit, skip database query

      await Promise.resolve()
      if (!isMounted.current) return

      setLoadingPaths((prev) => ({ ...prev, [path]: true }))
      try {
        const response = await fileService.fetchDir(groupId, projectId, path)
        setTree((prev) => ({ ...prev, [path]: response.data }))
      } catch (error) {
        console.error("Failed to load directory", error)
      } finally {
        if (isMounted.current) {
          setLoadingPaths((prev) => ({ ...prev, [path]: false }))
        }
      }
    },
    [projectId, tree, groupId]
  )

  useEffect(() => {
    if (projectId && groupId) {
      const timer = setTimeout(() => {
        fetchDir("")
      }, 0)

      return () => clearTimeout(timer)
    }
  }, [projectId, groupId, fetchDir])

  useEffect(() => {
    if (!activePath || !projectId) return

    const parts = activePath.split("/")
    let currentPath = ""

    parts.forEach((part, index) => {
      currentPath = currentPath ? `${currentPath}/${part}` : part
      if (index < parts.length - 1 || !part.includes(".")) {
        fetchDir(currentPath)
      }
    })
  }, [activePath, projectId, fetchDir])

  const renderNode = (path: string, depth = 0) => {
    const items = tree[path] || []

    return (
      <div
        className="space-y-1"
        style={{ paddingLeft: depth ? `${depth * 8}px` : "0px" }}
      >
        {items.map((file) => {
          const isCurrentActive = activePath === file.path
          const isOpen = activePath.startsWith(file.path)

          return (
            <div key={file.path} className="space-y-1">
              {file.type === "directory" ? (
                <Collapsible
                  open={isOpen}
                  onOpenChange={() => fetchDir(file.path)}
                >
                  <CollapsibleTrigger asChild>
                    <Link
                      to={`/projects/${projectId}/view?path=${file.path}&view=directory`}
                      className={`flex w-full cursor-pointer items-center gap-1.5 rounded-md p-1.5 text-sm hover:bg-muted ${
                        isCurrentActive
                          ? "bg-sidebar-accent font-medium text-sidebar-accent-foreground"
                          : ""
                      }`}
                    >
                      {isOpen ? (
                        <CaretDownIcon className="size-3.5 shrink-0" />
                      ) : (
                        <CaretRightIcon className="size-3.5 shrink-0" />
                      )}
                      <FolderIcon className="size-4 text-blue-500" />
                      <span className="truncate">{file.name}</span>
                      {loadingPaths[file.path] && (
                        <SpinnerIcon className="ml-auto size-3 animate-spin" />
                      )}
                      
                    </Link>
                  </CollapsibleTrigger>

                  <CollapsibleContent className="space-y-1">
                    {isOpen && renderNode(file.path, depth + 1)}
                  </CollapsibleContent>
                </Collapsible>
              ) : (
                <Link
                  to={`/projects/${projectId}/view?path=${file.path}&view=file`}
                  className={`flex cursor-pointer items-center gap-1.5 rounded-md p-1.5 text-sm hover:bg-muted ${
                    isCurrentActive
                      ? "bg-sidebar-accent font-medium text-sidebar-accent-foreground"
                      : ""
                  }`}
                >
                  <span className="w-3.5" />
                  <FileIcon className="size-4 text-muted-foreground shrink-0" />
                  <span className="truncate">{file.name}</span>
                </Link>
              )}
            </div>
          )
        })}
      </div>
    )
  }

  if (!projectId || !groupId)
    return (
      <div>
        <span>Failed to retrieve projectId or groupId</span>
        <Button onClick={() => navigate(-1)}>Go Back</Button>
      </div>
    )

  return (
    <div className="p-2">
      <span className="mb-2 block px-2 text-xs font-semibold text-muted-foreground">
        Repository Files
      </span>
      {renderNode("")}
    </div>
  )
}
