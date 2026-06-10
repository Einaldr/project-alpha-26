import { standardSchemaResolver } from "@hookform/resolvers/standard-schema"
import { Controller, useForm } from "react-hook-form"
import { z } from "zod"
import { Field, FieldError, FieldGroup, FieldLabel } from "../ui/field"
import { Input } from "../ui/input"
import { Card } from "../ui/card"
import { Button } from "../ui/button"
import { Separator } from "../ui/separator"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { useNavigate, useParams } from "react-router-dom"
import { useState } from "react"
import { toast } from "sonner"
import { useProjectsStore } from "@/hooks/useProjectsStore"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "../ui/select"
import { projectService } from "@/services/projectService"

const updateProjectFormSchema = z.object({
  name: z
    .string()
    .min(5, "Name must be at least 5 characters long.")
    .max(255, "Name can not be more than 255 characters long.")
    .nullable(),
  description: z.string().max(512).nullable(),
  default_branch: z.string(),
  background_image: z
    .file()
    .mime(["image/jpeg", "image/png", "image/webp"])
    .max(5_000_000)
    .nullable(),
})

export const ProjectUpdateForm = () => {
  const { projectId } = useParams()
  const { projects, branches, fetchProjects } = useProjectsStore()
  const { activeGroup } = useActiveGroupStore()
  const [isLoading, setIsLoading] = useState(false)
  const navigate = useNavigate()

  const project = projects.find((element) => element.id == projectId)

  const form = useForm<z.infer<typeof updateProjectFormSchema>>({
    resolver: standardSchemaResolver(updateProjectFormSchema),
    defaultValues: {
      name: project?.name || null,
      background_image: null,
      description: project?.description || null,
      default_branch: project?.default_branch || "N/A",
    },
  })

  if (!project || typeof projectId != "string") {
    return (
      <div className="h-full w-full items-center justify-center">
        <h1>Failed to retrieve project</h1>
        <Button onClick={() => navigate(-1)}>Go Back</Button>
      </div>
    )
  }

  if (!activeGroup?.id) {
    return (
      <div className="h-full w-full items-center justify-center">
        <h1>Failed to retrieve active group</h1>
        <Button onClick={() => navigate(-1)}>Go Back</Button>
      </div>
    )
  }

  if (!branches) {
    return (
      <div className="h-full w-full items-center justify-center">
        <h1>Failed to retrieve branches</h1>
        <Button onClick={() => navigate(-1)}>Go Back</Button>
      </div>
    )
  }

  const handleUpdate = async (
    data: z.infer<typeof updateProjectFormSchema>
  ) => {
    if (isLoading) throw new Error("Group is already being created.")
    setIsLoading(true)
    try {
      const formData = new FormData()

      formData.append("_method", "PATCH")

      if (data.name) formData.append("name", data.name)
      if (data.description) formData.append("description", data.description)

      if (data.background_image instanceof File) {
        formData.append("background_image", data.background_image)
      }

      formData.append("default_branch", data.default_branch)

      const apiCall = projectService.updateProject(activeGroup.id, projectId, formData)
      const timer = new Promise((resolve) => setTimeout(resolve, 1000))

      const [response] = await Promise.all([apiCall, timer])

      return response
    } catch {
      throw new Error("Failed to create the role")
    } finally {
      setIsLoading(false)
    }
  }

  async function onSubmit(data: z.infer<typeof updateProjectFormSchema>) {
    toast.promise(handleUpdate(data), {
      success: async () => {
        if (activeGroup) {
        fetchProjects(activeGroup)
        }
        navigate('/group/projects')
        return "Project successfully updated!"
      },
      error: (err) => {
        return err || "Something went wrong"
      },
    })
  }

  return (
      <Card className="w-full max-w-lg p-4 self-center">
        <form onSubmit={form.handleSubmit(onSubmit)}>
          <FieldGroup>
            <Controller
              name="name"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="creation-name">Name</FieldLabel>
                  <Input
                    {...field}
                    value={(field.value as string) ?? ""}
                    aria-invalid={fieldState.invalid}
                    type="text"
                    id="name"
                    placeholder={project.name}
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
          </FieldGroup>
          <Separator className="mt-4 mb-4 w-full" />
          <FieldGroup>
            <Controller
              name="description"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field>
                  <FieldLabel htmlFor="fieldupdate-desc">
                    Description
                  </FieldLabel>
                  <Input
                    {...field}
                    value={(field.value as string) ?? ""}
                    aria-invalid={fieldState.invalid}
                    type="text"
                    id="fieldupdate-desc"
                    placeholder=""
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
            <Controller
              name="default_branch"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field>
                  <FieldLabel htmlFor="fieldupdate-desc">
                    Default branch
                  </FieldLabel>
                  <Select
                    name={field.name}
                    value={field.value}
                    onValueChange={field.onChange}
                  >
                    <SelectTrigger
                      id="type-input"
                      aria-invalid={fieldState.invalid}
                    >
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent position="item-aligned">
                      {branches.branches.map((element) => (
                        <SelectItem
                          value={element}
                          textValue={element}
                          key={element}
                        >
                          {element}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
            <Controller
              name="background_image"
              control={form.control}
              render={({
                // eslint-disable-next-line @typescript-eslint/no-unused-vars
                field: { value, onChange, ...fieldProps },
                fieldState,
              }) => (
                <Field>
                  <FieldLabel htmlFor="fieldupdate-file">
                    Upload background image
                  </FieldLabel>
                  <Input
                    {...fieldProps}
                    type="file"
                    accept="image/*"
                    onChange={(event) => {
                      const file = event.target.files?.[0]
                      onChange(file)
                    }}
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
            <Separator className="mt-1 mb-1 w-full" />
            <Field>
              <Button size="lg" type="submit" className="w-full">
                Update
              </Button>
            </Field>
          </FieldGroup>
        </form>
      </Card>
  )
}
