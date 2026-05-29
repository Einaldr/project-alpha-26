import { Input } from "@/components/ui/input"
import { Field, FieldError, FieldGroup, FieldLabel } from "../ui/field"
import { Button } from "@/components/ui/button"
import { z } from "zod"
import { standardSchemaResolver } from "@hookform/resolvers/standard-schema"
import { Controller, useForm } from "react-hook-form"
import { userService } from "@/services/userService"
import { toast } from "sonner"

const loginFormSchema = z.object({
  name: z.string().min(4).max(255),
})

export default function UsernameSettingForm() {
  const form = useForm<z.infer<typeof loginFormSchema>>({
    resolver: standardSchemaResolver(loginFormSchema),
    defaultValues: {
      name: undefined,
    },
  })

  async function onSubmit(data: z.infer<typeof loginFormSchema>) {
    const loginWithDelay = async () => {
      const apiCall = userService.changeUsername(data)
      const timer = new Promise((resolve) => setTimeout(resolve, 1000))

      const [response] = await Promise.all([apiCall, timer])

      return response
    }

    toast.promise(loginWithDelay, {
      loading: "Verifying credentials...",
      success: (data) => {
        form.reset()
        return `Changed username to ${data.name}`
      },
      error: (err) => {
        return err?.response?.data?.message || "Something went wrong"
      },
    })
  }

  return (
    <form
      className="flex w-full flex-col items-center"
      onSubmit={form.handleSubmit(onSubmit)}
    >
      <FieldGroup className="rounded-sm border border-border p-8">
        <Controller
          name="name"
          control={form.control}
          render={({ field, fieldState }) => (
            <Field data-invalid={fieldState.invalid}>
              <FieldLabel htmlFor="updateuser-user">New Username:</FieldLabel>
              <Input
                {...field}
                aria-invalid={fieldState.invalid}
                type="text"
                id="updateuser-user"
                autoComplete="text"
                required
              />
              {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
            </Field>
          )}
        />

        <Field>
          <Button size="lg" type="submit" className="w-full">
            Login
          </Button>
        </Field>
      </FieldGroup>
    </form>
  )
}
