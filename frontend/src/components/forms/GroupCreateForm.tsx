import { standardSchemaResolver } from "@hookform/resolvers/standard-schema"
import { Controller, useForm, useWatch } from "react-hook-form"
import { z } from "zod"
import { Field, FieldError, FieldGroup, FieldLabel } from "../ui/field"
import { Input } from "../ui/input"
import { Card } from "../ui/card"
import { Button } from "../ui/button"
import { Separator } from "../ui/separator"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { Link, useNavigate } from "react-router-dom"
import { useEffect, useState } from "react"
import { toast } from "sonner"
import { groupService } from "@/services/groupService"
import { GroupTypeEnum } from "@/types/api"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "../ui/select"
import { Checkbox } from "../ui/checkbox"

const createGroupFormSchema = z
  .object({
    name: z
      .string()
      .min(5, "Name must be at least 5 characters long.")
      .max(255, "Name can not be more than 255 characters long."),
    icon: z
      .file()
      .mime(["image/jpeg", "image/png", "image/webp"])
      .max(5_000_000)
      .nullable(),
    billing_email: z.email().nullable(),
    parent_id: z.uuidv7().nullable(),
    is_private_child: z.boolean().optional(),
    type: z.enum(GroupTypeEnum),
  })
  .refine(
    (data) => {
      if (data.type != "team" && data.is_private_child) {
        return false
      }
      return true
    },
    {
      message: "A team cannot be private if it is not a team.",
      path: ["is_private_child"]
    }
  )
  .refine(
    (data) => {
      if (data.type == "individual") {
        return false
      }
      return true
    },
    {
      message: "You cannot create more workspaces.",
      path: ["type"]
    }
  )
  .refine((data)=>{
    if ((data.parent_id !== null && data.parent_id != "") && typeof data.is_private_child !== "boolean") {
      return false
    }
    return true
  }, {
    message: "Privacy status must be specified when a prent is set.",
    path: ["is_private_child"]
  })

export type createGroupFormSchemaType = z.infer<typeof createGroupFormSchema>

export const GroupCreateForm = () => {
  const { fetchGroups, setActiveGroup } = useActiveGroupStore()
  const [isLoading, setIsLoading] = useState(false)
  const navigate = useNavigate()

  const form = useForm<z.infer<typeof createGroupFormSchema>>({
    resolver: standardSchemaResolver(createGroupFormSchema),
    defaultValues: {
      name: "",
      icon: null,
      billing_email: null,
      parent_id: null,
      is_private_child: undefined,
      type: "org",
    },
  })

  const handleCreate = async (data: z.infer<typeof createGroupFormSchema>) => {
    if (isLoading) throw new Error("Group is already being created.")
    try {
      console.log(data)
      const apiCall = groupService.store(data)
      const timer = new Promise((resolve) => setTimeout(resolve, 1000))

      const [response] = await Promise.all([apiCall, timer])

      return response
    } catch {
      throw new Error("Failed to create the role")
    } finally {
      setIsLoading(false)
    }
  }

  async function onSubmit(data: z.infer<typeof createGroupFormSchema>) {
    toast.promise(handleCreate(data), {
      success: async (newGroup) => {
        fetchGroups()
        setActiveGroup(newGroup)
        navigate("/group/projects")
        return "Group successfully updated!"
      },
      error: (err) => {
        return err || "Something went wrong"
      },
    })
  }

  const groupTypeChoosen = useWatch({
    control: form.control,
    name: "type",
  })

  const parentId = useWatch({
    control: form.control,
    name: "parent_id"
  })

  useEffect(() => {
    if (groupTypeChoosen == "org") {
      form.setValue("parent_id", null)
      form.setValue("is_private_child", undefined)
    }
  }, [groupTypeChoosen, form])

  useEffect(() => {
    if (parentId !== null && parentId != "") {
      if (form.getValues("is_private_child") === undefined) {
        form.setValue("is_private_child", false)
      }
    } else {
      form.setValue("is_private_child", undefined)
    }
  }, [parentId, form])

  return (
    <div className="flex h-full w-full flex-col items-center justify-center self-center">
      <Card className="w-full max-w-lg p-4">
        <form onSubmit={form.handleSubmit(onSubmit)}>
          {/** Group name */}
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
                    placeholder="Enter the group name here"
                    required
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
          </FieldGroup>
          <Separator className="mt-4 mb-4 w-full" />

          {/** Group Info */}
          <FieldGroup>
            <Controller
              name="type"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="fieldupdate-email">
                    Group Type
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
                      <SelectItem value="org" textValue="Organization">Organization</SelectItem>
                      <SelectItem value="team" textValue="Team">Team</SelectItem>
                    </SelectContent>
                  </Select>
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
            <Controller
              name="billing_email"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="fieldcreate-email">
                    Billing Email
                  </FieldLabel>
                  <Input
                    {...field}
                    value={(field.value as string) ?? ""}
                    aria-invalid={fieldState.invalid}
                    type="email"
                    id="fieldcreate-email"
                    placeholder=""
                    autoComplete="email"
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
          </FieldGroup>
          <Separator className="mt-4 mb-4 w-full" />

          {/** Child details */}
          <FieldGroup>
            <Controller
              name="parent_id"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="fieldcreate-parent_id">
                    Parent ID:
                  </FieldLabel>
                  <Input
                    {...field}
                    value={field.value ?? ""}
                    aria-invalid={fieldState.invalid}
                    type="text"
                    id="fieldcreate-parent_id"
                    disabled={groupTypeChoosen != "team"}
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
            <Controller
              name="is_private_child"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field orientation="horizontal" data-invalid={fieldState.invalid}>
                  <Checkbox
                    id="fieldcreate-is_private_child"
                    disabled={groupTypeChoosen != "team"}
                    aria-invalid={fieldState.invalid}
                    onCheckedChange={field.onChange}
                    checked={!!field.value}
                  />
                  <FieldLabel htmlFor="fieldcreate-parent_id">
                    Is a private child?
                  </FieldLabel>
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
          </FieldGroup>
          <Separator className="mt-4 mb-4 w-full" />

          {/** Icon */}
          <FieldGroup>
            <Controller
              name="icon"
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
