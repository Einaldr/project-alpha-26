import { Field, FieldError, FieldGroup, FieldLabel } from "../ui/field"
import { Button } from "@/components/ui/button"
import { z } from "zod"
import { standardSchemaResolver } from "@hookform/resolvers/standard-schema"
import { Controller, useForm } from "react-hook-form"
import { toast } from "sonner"
import { GitAuthTypeSchema } from "@/types/api"
import { useProjectsStore } from "@/hooks/useProjectsStore"
import { useParams } from "react-router-dom"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "../ui/select"
import { projectService } from "@/services/projectService"
import { Card } from "../ui/card"
import { Input } from "../ui/input"

const projectSecretsFormSchema = z.object({
  auth_type: z.enum(GitAuthTypeSchema),
  access_token: z.string().max(255),
})

export default function SecretsSettingForm() {
  const { projectId } = useParams()
  const { projects, fetchProjects } = useProjectsStore()
  const { activeGroup } = useActiveGroupStore()

  const project = projects.find((element) => element.id == projectId)

  const form = useForm<z.infer<typeof projectSecretsFormSchema>>({
    resolver: standardSchemaResolver(projectSecretsFormSchema),
    defaultValues: {
      auth_type: project?.secrets?.auth_type,
      access_token: undefined,
    },
  })

  const availableAuthTypes: string[] = []
  GitAuthTypeSchema.forEach((authType) => {
    const splitted = authType.split(".")
    availableAuthTypes.push(splitted[2])
  })

  async function onSubmit(data: z.infer<typeof projectSecretsFormSchema>) {
    const updateWithDelay = async () => {
      const formData = new FormData()

      formData.append("_method", "PUT")

      formData.append("auth_type", data.auth_type)
      formData.append("access_token", data.access_token)

      if (!activeGroup?.id) {
        throw new Error(
          "Failed to update project secrets: activeGroup.id is null."
        )
      } else if (typeof projectId != "string") {
        throw new Error(
          "Failed to update project secrets: projectId isn't a string."
        )
      }

      const apiCall = projectService.updateSecrets(
        activeGroup?.id,
        projectId,
        formData
      )
      const timer = new Promise((resolve) => setTimeout(resolve, 1000))

      const [response] = await Promise.all([apiCall, timer])

      return response
    }

    toast.promise(updateWithDelay, {
      loading: "Updating username...",
      success: () => {
        if (activeGroup?.id) {
          fetchProjects(activeGroup)
        }
        return "Successfully updated project's secrets!"
      },
      error: (err) => {
        return err?.response?.data?.message || "Something went wrong"
      },
    })
  }

  return (
    <Card className="w-full max-w-lg self-center p-4">
      <form
        onSubmit={form.handleSubmit(onSubmit)}
      >
        <FieldGroup>
          <Controller
            name="auth_type"
            control={form.control}
            render={({ field, fieldState }) => (
              <Field>
                <FieldLabel htmlFor="fieldupdate-desc">
                  Auth Type
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
                    {GitAuthTypeSchema.map((element) => (
                      <SelectItem
                        value={element}
                        textValue={
                          availableAuthTypes[GitAuthTypeSchema.indexOf(element)]
                        }
                        key={element}
                      >
                        {availableAuthTypes[GitAuthTypeSchema.indexOf(element)]}
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
              name="access_token"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="creation-name">Authentication Token</FieldLabel>
                  <Input
                    {...field}
                    value={(field.value as string) ?? ""}
                    aria-invalid={fieldState.invalid}
                    type="text"
                    id="name"
                    placeholder={project?.secrets?.is_configured ? "Access token is configured!" : "Secrets are not configured"}
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />

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
