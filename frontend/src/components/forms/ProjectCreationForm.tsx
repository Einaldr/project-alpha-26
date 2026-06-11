import { standardSchemaResolver } from "@hookform/resolvers/standard-schema"
import { Controller, useForm } from "react-hook-form"
import { z } from "zod"
import { Field, FieldError, FieldGroup, FieldLabel } from "../ui/field"
import { Input } from "../ui/input"
import { Card } from "../ui/card"
import { Button } from "../ui/button"
import { Separator } from "../ui/separator"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { Link, useNavigate } from "react-router-dom"
import { useState } from "react"
import { toast } from "sonner"
import { GitAuthTypeSchema } from "@/types/api"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "../ui/select"
import { projectService } from "@/services/projectService"
import { useProjectsStore } from "@/hooks/useProjectsStore"

const createProjectFormSchema = z
  .object({
    name: z
      .string()
      .min(5, "Name must be at least 5 characters long.")
      .max(255, "Name can not be more than 255 characters long."),
    description: z.string().max(512).optional(),
    git_url: z.string().max(255),
    default_branch: z.string().max(255).optional(),
    background_image: z
      .file()
      .mime(["image/jpeg", "image/png", "image/webp"])
      .max(5_000_000)
      .nullable(),
    auth_type: z.enum(GitAuthTypeSchema).optional(),
    access_token: z.string().max(255).optional(),
  })
  .refine(
    (data) => {
      if (data.auth_type && !data.access_token) {
        return false
      }
      return true
    },
    {
      message:
        "You need to fill access token if authentication type is choosen.",
      path: ["access_token"],
    }
  )

export type createProjectFormSchemaType = z.infer<
  typeof createProjectFormSchema
>

export const ProjectCreateForm = () => {
  const {fetchProjects, changeProject} = useProjectsStore()
  const { activeGroup } = useActiveGroupStore()
  const [isLoading, setIsLoading] = useState(false)
  const navigate = useNavigate()

  const form = useForm<z.infer<typeof createProjectFormSchema>>({
    resolver: standardSchemaResolver(createProjectFormSchema),
    defaultValues: {
      name: "",
      description: undefined,
      git_url: undefined,
      default_branch: "main",
      background_image: null,
      auth_type: undefined,
      access_token: undefined,
    },
  })

  const handleCreate = async (
    data: z.infer<typeof createProjectFormSchema>
  ) => {
    if (isLoading) throw new Error("Group is already being created.")
    try {
      const formData = new FormData()

      formData.append("name", data.name)
      if (data.description) {
        formData.append("description", data.description)
      }
    
      formData.append("git_url", data.git_url)
      formData.append('default_branch', data?.default_branch || "main")
      if (data?.auth_type) formData.append('auth_type', data.auth_type)
      if (data?.auth_type && data?.access_token) formData.append('access_token', data.access_token)
      if (data.background_image instanceof File) {
        formData.append('background_image', data.background_image)
      }

      if (!activeGroup?.id) throw new Error("Failed to create new project: activeGroup's id is null")
      const apiCall = projectService.createProject(activeGroup?.id, formData)
      const timer = new Promise((resolve) => setTimeout(resolve, 1000))

      const [response] = await Promise.all([apiCall, timer])

      return response
    } catch {
      throw new Error("Failed to create the project")
    } finally {
      setIsLoading(false)
    }
  }

  async function onSubmit(data: z.infer<typeof createProjectFormSchema>) {
    toast.promise(handleCreate(data), {
      success: async (newProject) => {
        if (activeGroup?.id) {
            await fetchProjects(activeGroup.id)
            await changeProject(newProject)
        }
        navigate(`/projects/${newProject.id}`)
        return "Project successfully created!"
      },
      error: (err) => {
        return err || "Something went wrong"
      },
      loading: "Creating the project..."
    })
  }

  return (
    <div className="flex h-full w-full flex-col items-center justify-center self-center">
      <Card className="w-full max-w-lg p-4">
        <form onSubmit={form.handleSubmit(onSubmit)}>
          {/** Project name */}
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
                    placeholder="Enter the project name here"
                    required
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />

            {/** Project Info */}
            <Controller
              name="description"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="fieldcreate-desc">
                    Description
                  </FieldLabel>
                  <Input
                    {...field}
                    value={(field.value as string) ?? ""}
                    aria-invalid={fieldState.invalid}
                    type="text"
                    id="fieldcreate-desc"
                    placeholder="Enter the project's description here"
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
            <Controller
              name="git_url"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="fieldcreate-url">
                    Git Address
                  </FieldLabel>
                  <Input
                    {...field}
                    value={(field.value as string) ?? ""}
                    aria-invalid={fieldState.invalid}
                    type="text"
                    id="fieldcreate-url"
                    placeholder="Enter the git url here"
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
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="fieldcreate-branch">
                    Default branch
                  </FieldLabel>
                  <Input
                    {...field}
                    value={(field.value as string) ?? ""}
                    aria-invalid={fieldState.invalid}
                    type="text"
                    id="fieldcreate-branch"
                    placeholder="Enter the default branch's name here"
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
          </FieldGroup>
          <Separator className="mt-4 mb-4 w-full" />

          {/** Secrets */}
          <FieldGroup>
            <Controller
              name="auth_type"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="fieldupdate-auth">
                    Authentication Type
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
                      <SelectItem value={GitAuthTypeSchema[0]} textValue="http">
                        http
                      </SelectItem>
                      <SelectItem value="none" textValue="None" onClick={() => form.setValue('auth_type', undefined)}>
                        None
                      </SelectItem>
                    </SelectContent>
                  </Select>
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
            <Controller
              name="access_token"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="fieldcreate-access">
                    Access Token
                  </FieldLabel>
                  <Input
                    {...field}
                    value={field.value ?? ""}
                    aria-invalid={fieldState.invalid}
                    type="text"
                    id="fieldcreate-access"
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
          </FieldGroup>
          <Separator className="mt-4 mb-4 w-full" />

          {/** bg image */}
          <FieldGroup>
            <Controller
              name="background_image"
              control={form.control}
              render={({
                // eslint-disable-next-line @typescript-eslint/no-unused-vars
                field: { value, onChange, ...fieldProps },
                fieldState,
              }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="fieldupdate-file">
                    Upload icon
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
          </FieldGroup>
          <Separator className="mt-4 mb-4 w-full" />
          <Field>
            <Button size="lg" type="submit" className="w-full">
              Create Group
            </Button>
          </Field>
        </form>

        <Link to="/group/projects">
          <Button variant="destructive" size="lg" className="w-full">
            Cancel
          </Button>
        </Link>
      </Card>
    </div>
  )
}
