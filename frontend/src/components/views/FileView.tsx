import {
  useParams,
  useSearchParams,
} from "react-router-dom"
import { Card } from "../ui/card"
import { useEffect, useState } from "react"
import type { File } from "@/types/api"
import { fileService } from "@/services/fileService"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { DownloadIcon, FileCodeIcon, SpinnerIcon } from "@phosphor-icons/react"
import { Prism as SyntaxHighlighter } from "react-syntax-highlighter";
import { vscDarkPlus } from "react-syntax-highlighter/dist/esm/styles/prism";

export default function FileView() {
  const { projectId } = useParams<{ projectId: string }>()
  const [searchParams] = useSearchParams()
  const {activeGroup} = useActiveGroupStore()

  const path = searchParams.get("path") || ""
  const view = searchParams.get("view") || ""

  const isFileSelected = path !== "" && view === "file"

  const [fileContent, setFileContent] = useState<File | null>(null)
  
  const [prevPath, setPrevPath] = useState<string | null>(null);
    if (path !== prevPath) {
        setPrevPath(path);
        setFileContent(null);
    }

    const loading = isFileSelected && (!fileContent || fileContent.path !== path);

     useEffect(() => {
        if (isFileSelected && projectId && !fileContent) {
            let isMounted = true;
            if(!activeGroup?.id) return () => { isMounted = false; };
            
            const parts = path.split('/');
            const filename = parts.pop() || "";
            const pathname = parts.join('/');

            fileService.fetchFile(activeGroup.id, projectId, pathname, filename)
                .then((data) => {
                    if (isMounted) setFileContent(data);
                })
                .catch((err) => {
                    console.error("Failed to load file", err);
                    if (isMounted) setFileContent(null);
                });

            return () => { isMounted = false; };
        }
    }, [projectId, path, isFileSelected, fileContent, activeGroup?.id]);

  if (!isFileSelected)
    return (
      <div className="flex h-full w-full flex-col items-center justify-center">
        <Card className="border-2 border-dashed border-primary/75 p-4 font-semibold">
          Click on a file to start browsing!
        </Card>
      </div>
    )

    if (loading) {
        return (
            <div className="w-full h-full flex items-center justify-center">
                <SpinnerIcon className="animate-spin size-8 text-muted-foreground" />
            </div>
        );
    }

     const formatBytes = (bytes: number) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

   return (
        <div className="p-6 h-full overflow-y-auto flex flex-col gap-4">
            <div className="border rounded-xl p-6 bg-card text-card-foreground shadow-sm flex flex-col gap-4">
                
                {/* Header */}
                <div className="flex items-center justify-between border-b pb-4">
                    <div className="flex items-center gap-3">
                        <FileCodeIcon className="size-5 text-blue-500" />
                        <div>
                            <h2 className="font-semibold text-lg leading-tight">{fileContent?.name}</h2>
                            <span className="text-xs text-muted-foreground font-mono">
                                {fileContent ? formatBytes(fileContent.size) : ''}
                            </span>
                        </div>
                    </div>
                    {fileContent?.extension && (
                        <span className="text-xs text-muted-foreground uppercase font-mono bg-muted px-2.5 py-1 rounded-md border">
                            {fileContent.extension}
                        </span>
                    )}
                </div>

                {/* Body */}
                {fileContent?.is_binary ? (
                    <div className="flex flex-col items-center justify-center p-12 text-center text-muted-foreground border-2 border-dashed rounded-lg bg-muted/20">
                        <DownloadIcon className="size-8 mb-3 text-muted-foreground/60" />
                        <p className="font-semibold text-sm">Binary file detected</p>
                        <p className="text-xs text-muted-foreground max-w-xs mt-1 mb-4">
                            This file cannot be displayed directly as text.
                        </p>
                    </div>
                ) : (
                    <div className="rounded-lg border overflow-hidden bg-[#1e1e1e] text-left">
                        <SyntaxHighlighter
                            // Auto-detect language using the backend extension (e.g. 'ts', 'php', 'json')
                            language={fileContent?.extension} 
                            style={vscDarkPlus} // VS Code Dark Theme
                            showLineNumbers={true}
                            lineNumberStyle={{
                                color: "rgba(255, 255, 255, 0.2)",
                                minWidth: "2.25em",
                                textAlign: "right",
                                paddingRight: "1em",
                                userSelect: "none", // Prevent selecting line numbers during copying!
                            }}
                            customStyle={{
                                margin: 0,
                                padding: "1.25rem 1rem",
                                background: "transparent", // Let our container handle the background
                                fontSize: "0.8125rem", // Compact text-xs code font size
                                lineHeight: "1.6",
                            }}
                        >
                            {fileContent?.content || ""}
                        </SyntaxHighlighter>
                    </div>
                )}
            </div>
        </div>
    );
}
